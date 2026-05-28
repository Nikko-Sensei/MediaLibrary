<?php

namespace App\Contract;

/**
 * Defines methods for retrieving format, category,
 * and genre data from the data source.
 */
interface FormatInterface
{
    // Get format dropdown list
    public function getFormatDropDown($category = null);

    // Get category dropdown list
    public function getCategoryDropDown();

    // Get genres dropdown list
    public function getGenresDropDown($category = null);
}
