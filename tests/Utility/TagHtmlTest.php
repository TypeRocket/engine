<?php

namespace Utility;

use PHPUnit\Framework\TestCase;
use TypeRocket\Engine7\Utility\Html;

class TagHtmlTest extends TestCase
{
    /**
     * This test targets the __construct() function in the Tag class.
     * It creates an instance of the class with supplied parameters and validates the created object.
     * It specifically tests for the input parameters - tags, attributes, and nests.
     */
    public function testTagCreation(): void
    {
        // Use various constructions to test the constructor
        $tag1 = new Html("div", ['class' => 'class-name'], "Hello, World!");
        $tag2 = new Html("img", ['src' => 'image.jpg']);
        $nestedTags = new Html("section", [], [$tag1, $tag2]);

        // Assert the Tag was named correctly
        $this->assertEquals("div", $tag1->tagName());
        $this->assertEquals("img", $tag2->tagName());
        $this->assertEquals("section", $nestedTags->tagName());

        // Assert that 'img' tag is self-closing
        $this->assertTrue($tag2->isClosed());

        // Assert that inner HTML is correct
        $this->assertEquals("Hello, World!", $tag1->inner());

        // Assert that nested Tags are set correctly
        $this->assertStringContainsString('<div class="class-name">Hello, World!</div>', $tag1->getString());
        $this->assertStringContainsString('<img src="image.jpg" />', $nestedTags->inner());
    }

    /**
     * Test 'new' function of Tag class
     */
    public function testNew()
    {
        $tag = Html::new('div', ['class' => 'container'], 'test content');

        $this->assertInstanceOf(Html::class, $tag);
        $this->assertEquals('div', $tag->tagName());
        $this->assertFalse($tag->isClosed());
        $this->assertEquals('<div class="container">test content</div>', (string) $tag);
    }

    public function testNestAtTop()
    {
        // Html tag without nested elements
        $tag = new Html('html');

        // Add nested elements at the top
        $tag->nestAtTop('head');
        $tag->nestAtTop('body');

        // Access the protected property 'nest' through 'getNest' method
        $nest = $tag->getNest();

        // Check nested elements sequence
        $this->assertEquals('body', $nest[0]);
        $this->assertEquals('head', $nest[1]);
    }

    public function testNestAtTopWithTag()
    {
        $tag = new Html('div');

        // Add nested Tag element
        $nestedTag = new Html('p');
        $tag->nestAtTop($nestedTag);

        // Access the protected property 'nest' through 'getNest' method
        $nest = $tag->getNest();

        // Check nested element is a Tag instance
        $this->assertInstanceOf(Html::class, $nest[0]);
        $this->assertEquals($nestedTag, $nest[0]);
    }

    public function testNestAtTopWithArrayOfTags()
    {
        $tag = new Html('div');
        $nestedTags = [new Html('p'), new Html('span')];

        // Add an array of Tags
        $tag->nestAtTop($nestedTags);

        // Access the protected property 'nest' through 'getNest' method
        $nest = $tag->getNest();

        // Check if array of Tags was added correctly
        $this->assertInstanceOf(Html::class, $nest[0]);
        $this->assertInstanceOf(Html::class, $nest[1]);
        $this->assertEquals($nestedTags[0], $nest[1]);
        $this->assertEquals($nestedTags[1], $nest[0]);
    }

    public function testStaticCalls()
    {
        $tag = Html::div();
        $nestedTags = [Html::p(), Html::span()];

        // Add an array of Tags
        $tag->nestAtTop($nestedTags);

        // Access the protected property 'nest' through 'getNest' method
        $nest = $tag->getNest();

        // Check if array of Tags was added correctly
        $this->assertInstanceOf(Html::class, $nest[0]);
        $this->assertInstanceOf(Html::class, $nest[1]);
        $this->assertEquals($nestedTags[0], $nest[1]);
        $this->assertEquals($nestedTags[1], $nest[0]);
    }

    public function testTagCreationStaticCallsWithoutAttributes()
    {
        $tag = Html::div('content');

        // expected output
        $expected = '<div>content</div>';

        // assert that the actual output matches the expected output
        $this->assertEquals($expected, $tag);
    }

    public function testTagCreationStaticCallsCamelCase()
    {
        $tag = Html::DivOne('content');

        // expected output
        $expected = '<div-one>content</div-one>';

        // assert that the actual output matches the expected output
        $this->assertEquals($expected, $tag);
    }

    public function testTagCreationStaticCallsDynamicArgs()
    {
        $string1 = Html::kD('k', 'about', 'b')->getString();
        $string = Html::k('k', 'b')->getString();
        $string2 = Html::k('k', ['c' => 'x'], 'b')->getString();
        $div = Html::div(['class' => '"a!b!c"'], '"a!b!c"')->getString();
        $div2 = Html::div(['class' => '"a!b!c"'], '"a!b!c"', ['readonly' => 'readonly'])->getString();

        $this->assertEquals('<k-d>kaboutb</k-d>', $string1);
        $this->assertEquals('<k>kb</k>', $string);
        $this->assertEquals('<k c="x">kb</k>', $string2);
        $this->assertEquals('<div class="&quot;a!b!c&quot;">"a!b!c"</div>', $div);
        $this->assertEquals('<div class="&quot;a!b!c&quot;" readonly="readonly">"a!b!c"</div>', $div2);
    }
}