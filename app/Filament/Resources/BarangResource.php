<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Barang;
use Filament\Forms\Set;
use App\Models\Kategori;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BarangResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BarangResource\RelationManagers;
use App\Models\Supplier;
use Filament\Tables\Columns\ImageColumn;

class BarangResource extends Resource
{
    protected static ?string $model = Barang::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-on-square-stack';
    protected static ?string $label = 'Barang Masuk';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Select::make('supplier_id')
                    ->relationship('supplier', 'nama_pt')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, Set $set) {
                        $emailsupplier = Supplier::find($state);
                        $set('email', $emailsupplier->email ?? null);
                    }),
                TextInput::make('email')
                    ->disabled()
                    ->label('Email Supplier'),
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
                TextInput::make('kode_barang')
                    ->label('Kode Barang')
                    ->readOnly()
                    ->required(),
                TextInput::make('nama_barang')
                    ->required(),
                FileUpload::make('gambar'),

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
                ImageColumn::make('gambar'),
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
