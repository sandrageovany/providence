<?php
/** ---------------------------------------------------------------------
 * tests/helpers/UtilityHelpersTest.php
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2012 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 *
 * This source code is free and modifiable under the terms of 
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 * 
 * @package CollectiveAccess
 * @subpackage tests
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
 * 
 * ----------------------------------------------------------------------
 */
require_once(__CA_APP_DIR__."/helpers/utilityHelpers.php");

class UtilityHelpersTest extends PHPUnit_Framework_TestCase {
	# -------------------------------------------------------
	public function testCaFormatJson(){
		// actually valid JSON, perl-programmer style!
		$vs_test_json=<<<JSON
{"glossary": { "title": "example glossary", "GlossDiv": { "title": "S", "GlossList": {
"GlossEntry": { "ID": "SGML","SortAs": "SGML","GlossTerm": "Standard Generalized Markup Language",
"Acronym": "SGML", "Abbrev": "ISO 8879:1986", "GlossDef": { "para": "A meta-markup language,
used to create markup languages such as DocBook.", "GlossSeeAlso": ["GML", "XML"]
}, "GlossSee": "markup" } } } } }
JSON;
		$vs_formatted_json = caFormatJson($vs_test_json);
		$this->assertEquals(
			json_decode($vs_test_json,true),
			json_decode($vs_formatted_json,true)
		);
	}
	# -------------------------------------------------------
	public function testSanitizeStringHelper() {
		$this->assertEquals('test test', caSanitizeStringForJsonEncode('test test'));
		$this->assertEquals('"test" test', caSanitizeStringForJsonEncode('"test" test'));
		$this->assertEquals('(test) test', caSanitizeStringForJsonEncode('(test) test'));
	}
	# -------------------------------------------------------
	public function testParseLengthExpressionHelper() {
		$vm_ret = caParseLengthExpression("4x6", ['delimiter' => 'X', 'precision' => 0]);
		$this->assertInternalType('array', $vm_ret);
		$this->assertCount(2, $vm_ret);
		$this->assertEquals("4 in", $vm_ret[0]);
		$this->assertEquals("6 in", $vm_ret[1]);
		
		$vm_ret = caParseLengthExpression("4/6", ['delimiter' => '/', 'units' => 'mm']);
		$this->assertInternalType('array', $vm_ret);
		$this->assertCount(2, $vm_ret);
		$this->assertEquals("4.0 mm", $vm_ret[0]);
		$this->assertEquals("6.0 mm", $vm_ret[1]);
		
		$vm_ret = caParseLengthExpression("4x6cm", ['precision' => 0]);
		$this->assertInternalType('array', $vm_ret);
		$this->assertCount(2, $vm_ret);
		$this->assertEquals("4 cm", $vm_ret[0]);
		$this->assertEquals("6 cm", $vm_ret[1]);
		
		$vm_ret = caParseLengthExpression("4 1/2\"", ['precision' => 1]);
		$this->assertInternalType('array', $vm_ret);
		$this->assertCount(1, $vm_ret);
		$this->assertEquals("4.5 in", $vm_ret[0]);
		
		$vm_ret = caParseLengthExpression("4 ¾\"", ['precision' => 2]);
		$this->assertInternalType('array', $vm_ret);
		$this->assertCount(1, $vm_ret);
		$this->assertEquals("4.75 in", $vm_ret[0]);
		
		$vm_ret = caParseLengthExpression("4 ¾\"", ['precision' => 1]);
		$this->assertInternalType('array', $vm_ret);
		$this->assertCount(1, $vm_ret);
		$this->assertEquals("4.8 in", $vm_ret[0]);
		
		$vm_ret = caParseLengthExpression("4 ¾ x 4 ⅜ in", ['precision' => 1]);
		$this->assertInternalType('array', $vm_ret);
		$this->assertCount(2, $vm_ret);
		$this->assertEquals("4.8 in", $vm_ret[0]);
		$this->assertEquals("4.4 in", $vm_ret[1]);
		
		
		$vm_ret = caParseLengthExpression("4.151x6cm", ['precision' => 2]);
		$this->assertInternalType('array', $vm_ret);
		$this->assertCount(2, $vm_ret);
		$this->assertEquals("4.15 cm", $vm_ret[0]);
		$this->assertEquals("6.0 cm", $vm_ret[1]);
		
		$vm_ret = caParseLengthExpression("4 x 6cm x 8\"", ['precision' => 0]);
		$this->assertInternalType('array', $vm_ret);
		$this->assertCount(3, $vm_ret);
		$this->assertEquals("4 cm", $vm_ret[0]);
		$this->assertEquals("6 cm", $vm_ret[1]);
		$this->assertEquals("8 in", $vm_ret[2]);
		
		$vm_ret = caParseLengthExpression("4\" x 5", ['precision' => 0]);
		$this->assertInternalType('array', $vm_ret);
		$this->assertCount(2, $vm_ret);
		$this->assertEquals("4 in", $vm_ret[0]);
		$this->assertEquals("5 in", $vm_ret[1]);
		
		$vm_ret = caParseLengthExpression("4\" x 5", ['precision' => 1]);
		$this->assertInternalType('array', $vm_ret);
		$this->assertCount(2, $vm_ret);
		$this->assertEquals("4.0 in", $vm_ret[0]);
		$this->assertEquals("5.0 in", $vm_ret[1]);
	}
	# -------------------------------------------------------
}
