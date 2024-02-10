<?php

namespace App\Filament\Resources\AgendaResource\Pages;

use App\Enums\InvitationMessage;
use App\Filament\Helpers\SafeDeleteAction;
use App\Filament\Imports\VisitorImporter;
use App\Filament\Resources\AgendaResource;
use App\Models\Invitation;
use App\Models\Visitor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ManageAgendaVisitors extends ManageRelatedRecords
{
    protected static string $resource = AgendaResource::class;

    protected static string $relationship = 'visitors';

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    public static function getNavigationLabel(): string
    {
        return __('Visitors');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->translateLabel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone_number')
                    ->translateLabel()
                    ->dehydrateStateUsing(fn ($state) => normalize_phone_number($state)),
                Forms\Components\Textarea::make('address')
                    ->translateLabel()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Select::make('categories')
                    ->relationship('categories', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->label)
                    ->multiple()
                    ->translateLabel()
                    ->required()
                    ->preload(),
                Forms\Components\Fieldset::make(__('Invitation'))
                    ->relationship('invitation')
                    ->schema([
                        Forms\Components\TextInput::make('scan_url')
                            ->id('scan-url-text')
                            ->suffixActions([
                                Forms\Components\Actions\Action::make('copy')
                                    ->icon('heroicon-m-clipboard')
                                    ->extraAttributes([
                                        'class' => 'copy-btn',
                                        'data-clipboard-target' => '#scan-url-text',
                                    ]),
                                Forms\Components\Actions\Action::make('open_qr')
                                    ->icon('heroicon-m-arrow-up-right')
                                    ->color('success')
                                    ->url(fn ($record) => $record?->qr_url)
                                    ->openUrlInNewTab(),
                                Forms\Components\Actions\Action::make('share_to_whatsapp')
                                    ->icon('heroicon-m-share')
                                    ->color('info')
                                    ->url(fn ($record) => static::getWhatsAppLink($record))
                                    ->hidden(fn ($record) => empty($record?->visitor?->phone_number))
                                    ->openUrlInNewTab(),
                            ])
                            ->readOnly()
                            ->columnSpanFull()
                            ->hiddenOn('create'),
                        Forms\Components\TextInput::make('companion')
                            ->translateLabel()
                            ->numeric()
                            ->required()
                            ->minValue(fn (?Invitation $record) => -1 + $record?->checkIns()?->count() ?? 1)
                            ->columns(1),
                        Forms\Components\Hidden::make('code')->default(Invitation::generateCode())
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->translateLabel()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('genderCategories.name')
                    ->translateLabel()
                    ->searchable(),
                Tables\Columns\TextColumn::make('colorCategories.name')
                    ->translateLabel()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->translateLabel()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('invitation.companion')
                    ->label(__('Companion'))
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('check_ins_count')
                    ->label(__('Check in'))
                    ->alignCenter()
                    ->counts('checkIns')
                    ->sortable(),
                Tables\Columns\TextColumn::make('address')
                    ->translateLabel()
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('created_at')
                    ->translateLabel()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->translateLabel()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters($this->getTableCategoryFilters())
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\ImportAction::make()
                    ->translateLabel()
                    ->importer(VisitorImporter::class)
                    ->options([
                        'agendaId' => $this->getOwnerRecord()->getKey()
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('share_to_whatsapp')
                    ->label('Kirim WA')
                    ->icon('heroicon-m-share')
                    ->color('success')
                    ->url(fn ($record) => static::getWhatsAppLink($record->invitation))
                    ->hidden(fn ($record) => empty($record?->phone_number))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('copy')
                    ->label('Copy URL')
                    ->color('info')
                    ->icon('heroicon-m-clipboard')
                    ->extraAttributes(function (?Visitor $record) {
                        return [
                            'class' => 'copy-btn',
                            'data-clipboard-text' => $record->invitation?->scan_url,
                        ];
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->action(SafeDeleteAction::setUp(['checkIns'])),
            ]);
    }

    public function getTableCategoryFilters(): array
    {
        $relationships = [
            'genderCategories',
            'colorCategories',
        ];

        return collect($relationships)
            ->map(
                fn ($relationship) => Tables\Filters\SelectFilter::make(Str::snake($relationship))
                    ->translateLabel()
                    ->relationship($relationship, 'name')
                    ->multiple()
                    ->preload()
            )
            ->toArray();
    }

    /**
     * @return array
     */
    public function getExtraBodyAttributes(): array
    {
        return [
            'x-data' => RawJs::make(<<<JS
            {
                init() {
                    let clipboard = new ClipboardJS('.copy-btn');

                    clipboard.on('success', function(e) {
                        new FilamentNotification()
                            .title('URL undangan berhasil di copy')
                            .success()
                            .send()

                        e.clearSelection();
                    });
                }
            }
            JS),
        ];
    }

    public static function getWhatsAppLink(?Invitation $record): bool|string
    {
        if (!$record) {
            return false;
        }

        $visitor = $record->visitor;
        $agenda = $record->visitor->agenda;

        $message = InvitationMessage::apply($agenda->invitation_message, [
            InvitationMessage::NAME->value => $visitor->name,
            InvitationMessage::PHONE_NUMBER->value => $visitor->phone_number,
            InvitationMessage::ADDRESS->value => $visitor->address,
            InvitationMessage::COMPANION->value => $record->companion,
            InvitationMessage::URL->value => $record->scan_url,
            InvitationMessage::CODE->value => $record->code,
        ]);

        return sprintf(
            'https://wa.me/%s?text=%s',
            $record?->visitor?->phone_number,
            rawurlencode($message)
        );
    }
}
