<?php
namespace Traits;

use PHPUnit\Framework\TestCase;
use TypeRocket\Engine7\Elements\Traits\Attributes;

class AttributesTest extends TestCase
{
    private $attributes;

    protected function setUp(): void
    {
        $this->attributes = new class {
            use Attributes;
        };
    }

    public function testAttrWithNoArguments(): void
    {
        $actual = $this->attributes->attr();
        $this->assertEquals([], $actual);
    }

    public function testAttrWithArrayArgument(): void
    {
        $array = ['key' => 'value'];
        $this->attributes->attr($array);

        $actual = $this->attributes->attr();
        $expected = $array;
        $this->assertEquals($expected, $actual);
    }

    public function testAttrWithKeyValuePair(): void
    {
        $key = 'key2';
        $value = 'value2';
        $this->attributes->attr($key, $value);

        $actual = $this->attributes->attr($key);
        $expected = $value;
        $this->assertEquals($expected, $actual);
    }

    public function testAttrWithKeyOnly(): void
    {
        $key = 'key3';
        $value = null;
        $this->attributes->attr($key, $value);

        $actual = $this->attributes->attr($key);
        $expected = $value;
        $this->assertNull($actual);
    }

    public function testAttrReset(): void
    {
        $this->attributes->attrExtend(['class' => 'container', 'id' => 'containerId']);

        $this->assertEquals(['class' => 'container', 'id' => 'containerId'], $this->attributes->getAttributes());

        $this->attributes->attrReset(['style' => 'color:red']);

        $this->assertEquals(['style' => 'color:red'], $this->attributes->getAttributes());
    }

    public function testPopAllAttributes(): void
    {
        $testData = ['foo' => 'bar', 'baz' => 'qux'];
        $this->attributes->attrReset($testData);
        $poppedAttributes = $this->attributes->popAllAttributes();

        $this->assertSame($testData, $poppedAttributes);
    }

    /**
     * Ensure popAllAttributes resets attributes to empty array on the object itself.
     */
    public function testPopAllAttributesResetsArray(): void
    {
        $this->attributes->attrReset(['foo' => 'bar', 'baz' => 'qux']);
        $this->attributes->popAllAttributes();

        $this->assertEquals([], $this->attributes->getAttributes());
    }

    public function testPopAttribute(): void
    {
        //Arranging
        $attributeToBePopped = "value2";
        $attributes = ["attr1" => "value1", "attr2" => $attributeToBePopped];
        $this->attributes->attrReset($attributes);

        //Acting
        $poppedAttribute = $this->attributes->popAttribute();

        //Asserting
        $this->assertEquals($attributeToBePopped, $poppedAttribute);
        $this->assertArrayNotHasKey("attr2", $this->attributes->getAttributes());
    }

    public function testShiftAttribute(): void
    {
        // Setting Initial attributes
        $this->attributes->attrReset(['attr1' => 'value1', 'attr2' => 'value2', 'attr3' => 'value3']);

        // Use Case #1: Shifting the first attribute
        $shifted = $this->attributes->shiftAttribute();
        $remaining = $this->attributes->getAttributes();

        $this->assertEquals('value1', $shifted, 'The return value does not match the first attribute value.');
        $this->assertArrayNotHasKey('attr1', $remaining, 'Shifted attribute still exists in the remaining attributes');
    }

    public function testRemoveAttributeMethod(): void
    {
        // Setup test data
        $key = 'attributeKey';
        $value = 'attributeValue';

        // Set an attribute
        $this->attributes->setAttribute($key, $value);

        // Assert that the attribute was set
        $this->assertEquals($value, $this->attributes->getAttribute($key));

        // Call `removeAttribute` method
        $this->attributes->removeAttribute($key);

        // Assert that the attribute was removed
        $this->assertNull($this->attributes->getAttribute($key));
    }

    public function testAttrClass(): void
    {
        // Create a new Attributes instance
        $attributes = $this->attributes;

        // Define class attribute
        $classValue = 'my-class';

        // Set the class attribute
        $attributes->attrClass($classValue);

        // Check if the correct class attribute is set
        $this->assertEquals($classValue, $attributes->getAttribute('class'), "Class attribute does not match expected value");

        // Append a class to the class attribute
        $appendedClassValue = 'additional-class';
        $attributes->attrClass($appendedClassValue);

        // Check if the class attribute is updated with append value
        $this->assertEquals($classValue . ' ' . $appendedClassValue, $attributes->getAttribute('class'), "Appended class attribute does not match expected value");

        // Check if attrClass with no parameters returns the proper class string
        $this->assertEquals($classValue . ' ' . $appendedClassValue, $attributes->attrClass(), "Returned class attribute does not match expected value");
    }

    public function testAttrClassIfTrueCondition() : void
    {
        $this->attributes->attrClassIf(true, 'test');

        // Check that the 'class' key has the correct value
        $this->assertEquals('test', $this->attributes->getAttribute('class'));
    }

    public function testAttrClassIfFalseCondition() : void
    {
        $this->attributes->attrClassIf(false, 'test');

        // Check that the 'class' key has not been set
        $this->assertNull($this->attributes->getAttribute('class'));
    }

    public function testMaybeSetAttributeWithNonexistentAttribute(): void
    {
        $this->attributes->maybeSetAttribute('data-test', 'value');
        $this->assertEquals('value', $this->attributes->getAttribute('data-test'));
    }

    public function testMaybeSetAttributeWithExistingAttribute(): void
    {
        $this->attributes->attr('data-test', 'original');
        $this->attributes->maybeSetAttribute('data-test', 'new-value');
        $this->assertEquals('original', $this->attributes->getAttribute('data-test'));
    }
}