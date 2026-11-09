<?php
declare(strict_types=1);

class Validator
{
    public static function required(string $value, string $label): ?string
    {
        if (trim($value) === '') {
            return 'Поле «' . $label . '» обязательно';
        }
        return null;
    }

    public static function login(string $value): ?string
    {
        if (trim($value) === '') {
            return 'Логин обязателен';
        }
        if (!preg_match('/^[A-Za-z0-9]{6,}$/', $value)) {
            return 'Логин: латиница и цифры, минимум 6 символов';
        }
        return null;
    }

    public static function password(string $value): ?string
    {
        if ($value === '') {
            return 'Пароль обязателен';
        }
        if (mb_strlen($value) < 6) {
            return 'Пароль должен содержать минимум 6 символов';
        }
        return null;
    }

    public static function fullName(string $value): ?string
    {
        if (trim($value) === '') {
            return 'ФИО обязательно';
        }
        if (!preg_match('/^[А-Яа-яЁё\s]+$/u', $value)) {
            return 'ФИО: только кириллица и пробелы';
        }
        return null;
    }

    public static function normalizePhone(string $value): ?string
    {
        $digits = preg_replace('/\D+/', '', $value) ?? '';

        if (strlen($digits) === 11 && $digits[0] === '8') {
            $digits = '7' . substr($digits, 1);
        }
        if (strlen($digits) === 10 && $digits[0] === '9') {
            $digits = '7' . $digits;
        }
        if (strlen($digits) !== 11 || $digits[0] !== '7') {
            return null;
        }

        return '+7(' . substr($digits, 1, 3) . ')' . substr($digits, 4, 3)
            . '-' . substr($digits, 7, 2) . '-' . substr($digits, 9, 2);
    }

    public static function phone(string $value): ?string
    {
        if (trim($value) === '') {
            return 'Телефон обязателен';
        }
        if (self::normalizePhone($value) === null) {
            return 'Телефон в формате +7(XXX)XXX-XX-XX';
        }
        return null;
    }

    public static function email(string $value): ?string
    {
        if (trim($value) === '') {
            return 'E-mail обязателен';
        }
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return 'Некорректный e-mail';
        }
        return null;
    }

    public static function date(string $value): ?string
    {
        if (trim($value) === '') {
            return 'Дата обязательна';
        }
        if (!preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $value)) {
            return 'Дата в формате ДД.ММ.ГГГГ';
        }

        $date = DateTimeImmutable::createFromFormat('!d.m.Y', $value);
        if ($date === false || $date->format('d.m.Y') !== $value) {
            return 'Некорректная дата';
        }

        return null;
    }

    public static function time(string $value): ?string
    {
        if (trim($value) === '') {
            return 'Время обязательно';
        }
        if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $value)) {
            return 'Время в формате ЧЧ:ММ';
        }
        if ($value < '09:00' || $value > '20:00') {
            return 'Клиника работает с 09:00 до 20:00';
        }
        return null;
    }
}