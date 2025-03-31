<?php

/**
 * -------------------------------------------------------------------------
 * Mreporting plugin for GLPI
 * -------------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of Mreporting.
 *
 * Mreporting is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Mreporting is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Mreporting. If not, see <http://www.gnu.org/licenses/>.
 * -------------------------------------------------------------------------
 * @copyright Copyright (C) 2003-2023 by Mreporting plugin team.
 * @license   GPLv2 https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/pluginsGLPI/mreporting
 * -------------------------------------------------------------------------
 */

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}

class PluginMreportingPdf extends TCPDF
{
    /**
     * Create PDF header and initialize presentation
     */
    // @codingStandardsIgnoreStart
    public function Init()
    {
        // @codingStandardsIgnoreEnd
        /** @var array $CFG_GLPI */
        global $CFG_GLPI;

        $title   = __('GLPI statistics reports', 'mreporting');
        $creator = __('Automaticaly generated by GLPI', 'mreporting');
        $version = plugin_version_mreporting();
        $author  = $CFG_GLPI['version'] . ' - ' . $version['name'] . ' v' . $version['version'];

        $this->SetCreator($creator);
        $this->SetAuthor($author);
        $this->SetTitle($title);

        $this->SetFontSize(10);
        $this->SetMargins(20, 25);

        $this->SetAutoPageBreak(true);

        $this->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
        $this->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->AddPage();
    }

    /**
     * Insert content and graphs
     *
     * @param array $images Array of reports
     */
    // @codingStandardsIgnoreStart
    public function Content($images)
    {
        // @codingStandardsIgnoreEnd
        $images_lengh = sizeof($images);
        $i            = 0;
        foreach ($images as $image) {
            $i++;
            $file = '@' . base64_decode($image['base64']);
            $w    = 210 - PDF_MARGIN_LEFT * 2;

            if ($image['width'] == 0) {
                continue;
            }

            $h = floor(($image['height'] * $w) / $image['width']);
            $this->Image($file, null, null, $w, $h);
            $this->Ln($h);

            $this->writeHTMLCell(0., 0., null, null, $image['title'], 0, 1, false, true, 'C');
            if ($i < $images_lengh) {
                $this->AddPage();
            }
        }
    }

    /**
     * Create the PDF footer
     */
    // @codingStandardsIgnoreStart
    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFontSize(8);
        $this->writeHTMLCell(0., 0., null, null, date('Y-m-d H:i:s'), 0, 0, false, true, 'R');
    }
}
