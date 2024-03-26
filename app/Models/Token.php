<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    // Especifica el nombre de la tabla si no sigue las convenciones de Laravel
    protected $table = 'token';

    // Laravel espera una columna 'id' por defecto. Dado que tu clave primaria es 'token', necesitas especificarlo.
    protected $primaryKey = 'token';

    // Indica que la clave primaria no es un número entero.
    public $incrementing = false;

    // Indica el tipo de dato de la clave primaria, en este caso 'string'.
    protected $keyType = 'string';

    // Desactiva las marcas de tiempo 'updated_at', pero mantiene 'created_at'.
    // No es necesario si solo quieres evitar el error por falta de 'updated_at', pero se incluye por completitud.
    public $timestamps = true;
    const UPDATED_AT = null;

    // Especifica los atributos que pueden ser asignados masivamente.
    protected $fillable = ['token'];

    // Aunque no es estrictamente necesario, puedes especificar que 'created_at' se maneje como instancia de Carbon.
    protected $dates = ['created_at'];
}
