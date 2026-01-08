<?php

namespace App\Enums;

enum BookStatus: string
{
    case READING = 'reading';
    case READ = 'read';
    case PLANNING = 'planning_to_read';

    /**
     * Human-readable label for each book status
     */
    public function label(): string
    {
        return match($this) {
            self::READING => 'Lendo',
            self::READ => 'Lido',
            self::PLANNING => 'Quero Ler',
        };
    }

    /**
     * Color/class associated with each book status
     */
    public function color(): string
    {
        return match($this) {
            self::READING => 'blue',
            self::READ => 'green',
            self::PLANNING => 'gray',
        };
    }

    /**
     * Icon associated with each book status
     */
    public function icon(): string
    {
        return match($this) {
            self::READING => 'pi pi-book',
            self::READ => 'pi pi-check-circle',
            self::PLANNING => 'pi pi-bookmark',
        };
    }
}