<?php

namespace App\Http\Exports;

abstract class BaseExport
{
    protected $search;
    protected $filters;
    protected $data;

    public function __construct($search = null, $filters = [])
    {
        $this->search = $search;
        $this->filters = $filters;
        $this->loadData();
    }

    /**
     * Charger les données (à implémenter dans les classes enfants)
     */
    abstract protected function loadData();

    /**
     * Définir les en-têtes (à implémenter dans les classes enfants)
     */
    abstract public function headings(): array;

    /**
     * Définir les données (à implémenter dans les classes enfants)
     */
    abstract public function collection();

    /**
     * Télécharger le fichier (à implémenter dans les classes enfants)
     */
    abstract public function download();

    /**
     * Obtenir les statistiques communes
     */
    public function getBaseStats()
    {
        return [
            'search_term' => $this->search,
            'filters_applied' => $this->filters,
            'export_date' => now()->format('d/m/Y H:i:s'),
            'export_timestamp' => now()->timestamp
        ];
    }

    /**
     * Formater une date pour l'exportation
     */
    protected function formatDate($date, $format = 'd/m/Y')
    {
        return $date ? $date->format($format) : '';
    }

    /**
     * Formater un prix pour l'exportation
     */
    protected function formatPrice($price, $decimals = 2)
    {
        return number_format($price, $decimals, '.', '');
    }

    /**
     * Nettoyer une chaîne pour l'exportation
     */
    protected function cleanString($string)
    {
        return trim(strip_tags($string));
    }

    /**
     * Générer un nom de fichier unique
     */
    protected function generateFilename($prefix, $extension)
    {
        $timestamp = date('Y-m-d_H-i-s');
        $search_suffix = $this->search ? '_' . str_replace(' ', '-', $this->search) : '';
        
        return "{$prefix}{$search_suffix}_{$timestamp}.{$extension}";
    }
}
