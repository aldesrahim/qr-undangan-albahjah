<?php

namespace App\Filament\Imports;

use App\Enums\CategoryType;
use App\Models\Category;
use App\Models\Invitation;
use App\Models\Visitor;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class VisitorImporter extends Importer
{
    protected static ?string $model = Visitor::class;

    protected array $categoriesCached = [];

    protected array $dataBefore = [];

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->rules(['required', 'max:255'])
                ->label('Nama')
                ->example('Abdurrahman'),
            ImportColumn::make('address')
                ->label('Alamat')
                ->example('Jl. Kemanggisan'),
            ImportColumn::make('phone_number')
                ->label('Nomor Telepon')
                ->example('6283893962489'),
            ImportColumn::make('companion')
                ->label('Jumlah Pendamping')
                ->integer()
                ->example(2),
            ImportColumn::make('gender_category')
                ->label('Jenis Kelamin')
                ->example('Banat'),
            ImportColumn::make('color_category')
                ->label('Kategori Warna')
                ->example('Gold'),
        ];
    }

    public function resolveRecord(): ?Visitor
    {
        return Visitor::firstOrNew([
            'agenda_id' => $this->options['agendaId'],
            'name' => $this->data['name'],
            //'phone_number' => normalize_phone_number($this->data['phone_number']),
        ]);
    }

    public function getCategory($name, CategoryType $type): Category
    {
        $key = $name . $type->value;

        $this->categoriesCached[$key] ??= Category::query()
            ->firstOrCreate([
                'type' => $type,
                'name' => $name,
            ]);

        return $this->categoriesCached[$key];
    }

    protected function beforeFill(): void
    {
        $this->data['phone_number'] = normalize_phone_number($this->data['phone_number']);
        $this->dataBefore = $this->data;
        $this->data = [
            'name' => $this->data['name'],
            'address' => $this->data['address'],
            'phone_number' => $this->data['phone_number'],
        ];
    }

    protected function afterFill(): void
    {
        $this->data = $this->dataBefore;
    }

    protected function afterValidate(): void
    {
        $this->getCategory(
            $this->originalData['gender_category'],
            CategoryType::GENDER,
        );
        $this->getCategory(
            $this->originalData['color_category'],
            CategoryType::COLOR,
        );
    }

    protected function afterSave(): void
    {
        /** @var Visitor $record */
        $record = $this->record;

        $record->invitation()->create([
            'code' => Invitation::generateCode(),
            'companion' => $this->originalData['companion'] ?? 0
        ]);

        $categories = collect([
            $this->getCategory(
                $this->originalData['gender_category'],
                CategoryType::GENDER,
            ),
            $this->getCategory(
                $this->originalData['color_category'],
                CategoryType::COLOR,
            ),
        ]);

        $record->categories()->sync($categories->pluck('id'));
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Impor tamu undangan sudah selesai dan ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' berhasil diimpor.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' gagal diimpor.';
        }

        return $body;
    }
}
