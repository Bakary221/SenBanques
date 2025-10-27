<?php

namespace App\Models;

use App\Casts\Solde;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CompteBancaire extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'compte_bancaires';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'numero',
        'type_compte',
        'statut',
        'motif_blocage',
        'user_id',
    ];

    protected $casts = [
        'solde' => Solde::class,
    ];

    protected $appends = ['solde'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }

            // Générer le numéro automatiquement si non fourni
            if (empty($model->numero)) {
                $lastAccount = static::withTrashed()->orderBy('numero', 'desc')->first();

                if ($lastAccount) {
                    $lastNumber = (int) substr($lastAccount->numero, 1);
                    $newNumber = $lastNumber + 1;
                } else {
                    $newNumber = 1;
                }

                $model->numero = 'C' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
            }
        });

        // Soft delete activé automatiquement
    }

    /**
     * Scope local pour récupérer un compte par son numéro
     */
    public function scopeNumero($query, $numero)
    {
        return $query->where('numero', $numero);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'user_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Scope local pour récupérer les comptes d'un client basé sur son téléphone
     */
    public function scopeClient($query, $telephone)
    {
        return $query->whereHas('user', function ($q) use ($telephone) {
            $q->where('telephone', $telephone);
        });
    }

    /**
     * Scope local pour filtrer par statut
     */
    public function scopeStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }

    /**
     * Bloquer un compte
     */
    public function bloquer(string $motif): bool
    {
        $this->statut = 'bloque';
        $this->motif_blocage = $motif;
        return $this->save();
    }

    /**
     * Activer un compte
     */
    public function activer(): bool
    {
        $this->statut = 'actif';
        $this->motif_blocage = null;
        return $this->save();
    }

    /**
     * Désactiver un compte
     */
    public function desactiver(): bool
    {
        $this->statut = 'inactif';
        $this->motif_blocage = null;
        return $this->save();
    }

    /**
     * Vérifier si le compte est actif
     */
    public function estActif(): bool
    {
        return $this->statut === 'actif';
    }

    /**
     * Vérifier si le compte est bloqué
     */
    public function estBloque(): bool
    {
        return $this->statut === 'bloque';
    }

    /**
     * Vérifier si le compte est inactif
     */
    public function estInactif(): bool
    {
        return $this->statut === 'inactif';
    }
}
