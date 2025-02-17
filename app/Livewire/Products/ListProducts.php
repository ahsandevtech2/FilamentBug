<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Table;
use Livewire\Component;
use Illuminate\Contracts\View\View;

class ListProducts extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Product::query())
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->limit(50)
                    ->wrap()
                    ->sortable()
                    ->toggleable()
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('contact_number')
                    ->limit(50)
                    ->wrap()
                    ->sortable()
                    ->toggleable()
                    ->searchable(isIndividual: true),
            ])
            ->filters([
                QueryBuilder::make()
                    ->constraints([
                        QueryBuilder\Constraints\TextConstraint::make('name')->label('name'),
                        QueryBuilder\Constraints\TextConstraint::make('contact_number')->label('contact number'),
                    ])
            ], Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->actions([
                ActionGroup::make([
                    Action::make('Edit')
                        ->action(function (Product $costCenter) {
                            $this->test();
                        }),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //
                ]),
            ])
            ->deferLoading()
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public function removeTableFilters(): void
    {
        $filters = $this->getTable()->getFilters();

        if (isset($filters['queryBuilder'])) {
            unset($filters['queryBuilder']);
        }

        foreach ($filters as $filterName => $filter) {
            $this->removeTableFilter(
                $filterName,
                isRemovingAllFilters: true,
            );
        }

        $this->resetTableSearch();
        $this->resetTableColumnSearches();

        if ($this->getTable()->hasDeferredFilters()) {
            $this->applyTableFilters();

            return;
        }

        $this->handleTableFilterUpdates();
    }

    protected function getListeners()
    {
        return array_merge(
            parent::getListeners(),
            [
                'CostCentersUpdated' => '$refresh',
            ]
        );
    }

    public function render(): View
    {
        return view('livewire.products.list-products');
    }
}
