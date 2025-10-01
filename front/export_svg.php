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

header('Expires: Mon, 26 Nov 1962 00:00:00 GMT');
header('Pragma: private'); /// IE BUG + SSL
header('Cache-control: private, must-revalidate'); /// IE BUG + SSL
header('Content-disposition: attachment; filename=export.svg');
header('Content-type: image/svg+xml');

$svg_content = (string) ($_REQUEST['svg_content'] ?? '');
$svg_content = htmlspecialchars($svg_content, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

$svg_content = preg_replace(
    '/^<svg\b/i',
    '<svg version="1.1" baseProfile="full" xmlns="http://www.w3.org/2000/svg"',
    $svg_content,
);

echo $svg_content;
