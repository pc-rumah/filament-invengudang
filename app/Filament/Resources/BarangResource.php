<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarangResource\Pages;
use App\Filament\Resources\BarangResource\RelationManagers;
use App\Models\Barang;
use App\Models\Kategori;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BarangResource extends Resource
{
    protected static ?string $model = Barang::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-on-square-stack';
    protected static ?string $label = 'Barang Masuk';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('kode_barang')
                    ->label('Kode Barang')
                    ->readOnly()
                    ->required(),
                Select::make('supplier_id')
                    ->relationship('supplier', 'nama_pt')
                    ->required(),
                TextInput::make('nama_barang')
                    ->required(),
                Select::make('kategori_id')
                    ->label('Kategori')
                    ->options(
                        Kategori::all()->pluck('nama_kategori', 'id')->toArray()
                    )
                    ->reactive()
                    ->afterStateUpdated(function ($state, Set $set) {
                        $kategori = Kategori::find($state);

                        if ($kategori) {
                            $jumlahBarang = Barang::where('kategori_id', $state)->count() + 1;

                            // Format kode barang
                            $kodeBarang = $kategori->kode_barang . '-' . str_pad($jumlahBarang, 4, '0', STR_PAD_LEFT);

                            // Set nilai kode_barang
                            $set('kode_barang', $kodeBarang);
                        }
                    }),
                TextInput::make('harga')
                    ->numeric(),
                TextInput::make('stok')
                    ->numeric(),
                TextInput::make('lokasi_gudang'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_barang'),
                TextColumn::make('supplier.nama_pt'),
                TextColumn::make('nama_barang'),
                TextColumn::make('harga'),
                TextColumn::make('stok'),
                TextColumn::make('lokasi_gudang'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBarangs::route('/'),
            'create' => Pages\CreateBarang::route('/create'),
            'edit' => Pages\EditBarang::route('/{record}/edit'),
        ];
    }
}
