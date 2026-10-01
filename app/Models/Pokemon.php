<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pokemon extends Model
{
    use HasFactory;

    /**
     * El nombre de la tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'pokemon';

    /**
     * Indica si el ID es autoincremental.
     * Es falso porque usamos el número oficial de la Pokédex como clave primaria.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * El tipo de la clave primaria.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'genus',
        'height',
        'weight',
        'sprite',
        'types',
        'description',
        'hp',
        'attack',
        'defense',
        'special_attack',
        'special_defense',
        'speed',
    ];

    /**
     * Conversión de atributos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'height' => 'float',
            'weight' => 'float',
            'types' => 'array',
            'hp' => 'integer',
            'attack' => 'integer',
            'defense' => 'integer',
            'special_attack' => 'integer',
            'special_defense' => 'integer',
            'speed' => 'integer',
        ];
    }

    /**
     * Formatea el modelo a la estructura exacta esperada por la vista y AJAX.
     */
    public function toFrontendArray(): array
    {
        $localSpritePath = public_path("images/sprites/{$this->id}.png");
        $spriteUrl = file_exists($localSpritePath)
            ? asset("images/sprites/{$this->id}.png")
            : ($this->sprite ?? asset("images/sprites/{$this->id}.png"));

        return [
            'id' => $this->id,
            'name' => $this->name,
            'genus' => $this->genus ?? 'Pokémon de Kanto',
            'height' => (float) $this->height,
            'weight' => (float) $this->weight,
            'sprite' => $spriteUrl,
            'types' => is_array($this->types) ? $this->types : json_decode($this->types, true),
            'description' => $this->description ?? 'Sin descripción disponible.',
            'from_local_db' => true,
            'stats' => [
                'hp' => $this->hp,
                'attack' => $this->attack,
                'defense' => $this->defense,
                'special_attack' => $this->special_attack,
                'special_defense' => $this->special_defense,
                'speed' => $this->speed,
            ],
        ];
    }
}
