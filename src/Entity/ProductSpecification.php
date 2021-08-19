<?php


namespace App\Entity;


class ProductSpecification
{
    public const NAME_COLOR = 'color';
    public const NAME_SIZE = 'size';
    public const NAME_MATERIAL = 'material';
    public const NAME_COUNTRY = 'country';
    public const NAME_GENDER = 'gender';

    private $name; // - название характеристики - выпадающий список на форме, имя не может быть пустым

    private $value; // - текстовое поля для значения характеристики, значение не может быть пустым

}