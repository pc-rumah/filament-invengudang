<?php

namespace App\Filament\Resources;

use Closure;
use Filament\Forms;
use Filament\Tables;
use App\Models\Barang;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\BarangKeluar;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BarangKeluarResource\Pages;
use App\Filament\Resources\BarangKeluarResource\RelationManagers;
use Filament\Forms\Components\DatePicker;

class BarangKeluarResource extends Resource
{
    protected static ?string $model = BarangKeluar::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-on-square-stack';
    protected static ?string $label = 'Barang Keluar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('barang_id')
                    ->relationship('barang', 'nama_barang')
                    ->required()
                    ->label('Nama Barang')
                    ->reactive()
                    ->rule(function (callable $get) {
                        $stok = $get('stok');
                        return "max:$stok";
                    })
                    ->afterStateUpdated(function ($state, callable $get, Set $set) {
                        $stok = $get('stok');
                        if ($state > $stok) {
                            $set('jumlah_barang_keluar', $stok);
                        }
                        $barang = Barang::find($state);

                        $set('stok', $barang->stok);
                    }),
                TextInput::make('stok'),
                TextInput::make('jumlah_barang_keluar')
                    ->required()
                    ->label('Jumlah Barang Keluar')
                    ->type('number'),
                TextInput::make('penerima')
                    ->required(),
                Textarea::make('keterangan')
                    ->required(),
                DatePicker::make('tanggal_keluar')
                    ->required()
                    ->default(now())
                    ->label('Tanggal Keluar'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('barang.nama_barang'),
                TextColumn::make('jumlah_barang_keluar'),
                TextColumn::make('penerima'),
                TextColumn::make('keterangan'),
                TextColumn::make('tanggal_keluar')
                    ->dateTime('d F y'),
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
            'index' => Pages\ListBarangKeluars::route('/'),
            'create' => Pages\CreateBarangKeluar::route('/create'),
            'edit' => Pages\EditBarangKeluar::route('/{record}/edit'),
        ];
    }
}
