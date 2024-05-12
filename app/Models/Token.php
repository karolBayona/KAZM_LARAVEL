<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $table = 'token';  // El nombre de la tabla es explícitamente declarado como 'token'

    protected $primaryKey = 'token';  // La clave primaria es 'token'

    public $incrementing = false;  // No se autoincrementa, visibilidad pública necesaria

    protected $keyType = 'string';  // Tipo de clave primaria es string

    public $timestamps = true;  // Los timestamps están habilitados

    public const UPDATED_AT = null;  // Desactiva la actualización automática de 'updated_at', constante pública

    protected $fillable = ['token'];  // Los atributos que se pueden asignar masivamente

    protected $dates = ['created_at'];  // Trata 'created_at' como una instancia de Carbon
}
