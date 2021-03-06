<?php

/* DictionBase.php
 *
 * Copyright 2009 Julian Calaby <julian.calaby@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 3 of the License, or (at your
 * option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License
 * for more details.
 * 
 * You should have received a copy of the GNU General Public License along
 * with this program.  If not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */

class DictionBase {
	var $lang;

	function __construct($lang = null) {
		if( $lang == null ) {
			require_once("LangEN.php");

			$lang = new LangEN();
		}

		$this->setLang($lang);
	}

	function setLang($lang) {
		$this->lang = $lang;
	}
}

?>
