<?php

namespace App\Filament\Helpers;

use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class SafeDeleteAction
{
    public static function setUp($relations): \Closure
    {
        return function ($action) use ($relations): void {
            $result = $action->process(function (Model $record, $action) use ($relations) {
                foreach (Arr::wrap($relations) as $relation) {
                    $previousRelation = null;
                    $nestedRelations = str($relation)->contains('.')
                        ? explode('.', $relation)
                        : Arr::wrap($relation);

                    foreach ($nestedRelations as $nestedRelation) {
                        $previousRelation ??= $record;

                        if (!method_exists($previousRelation, $nestedRelation)) {
                            throw new \InvalidArgumentException(
                                sprintf('Relation %s not found in %s model', $nestedRelation, $record::class)
                            );
                        }

                        if ($previousRelation->$nestedRelation()->exists()) {
                            Notification::make()
                                ->danger()
                                ->title('Tidak bisa dihapus')
                                ->body('Data ini tidak dapat dihapus karena sudah terikat dengan data lain')
                                ->send();

                            $action->dispatchFailureRedirect();

                            return false;
                        }

                        $previousRelation = $previousRelation->$nestedRelation();
                    }
                }

                return $record->delete();
            });

            if (! $result) {
                $action->failure();

                return;
            }

            $action->success();
        };
    }
}
