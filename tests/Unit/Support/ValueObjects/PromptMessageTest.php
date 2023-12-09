<?php

namespace Tests\Unit\Support\ValueObjects;

use Illuminate\Foundation\Testing\WithFaker;
use Support\ValueObjects\PromptMessage;
use Tests\TestCase;

class PromptMessageTest extends TestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test  */
    public function itShouldThrowErrorWhenReplacersIsInvalid()
    {
        $this->assertThrows(function () {
            PromptMessage::make($this->faker->text(1000), ['-_-invalid_PLACEHOLDER_/' => 'value']);
        }, expectedMessage: sprintf("this replacer: %s must be string, starts with '_' and ends with '_'", '-_-invalid_PLACEHOLDER_/'));
    }

    /** @test */
    public function itShouldAlwaysCastValuesToString()
    {
        $replacers = [
            '_place_holder1_' => 'string',
            '_place_holder2_' => 100.1,
            '_place_holder3_' => 4000,
            '_place_holder4_' => [1, 2, 3, 4, 5, 6],
            '_place_holder5_' => ['strings'],
            '_place_holder6_' => ['strings'],
            '_place_holder7_' => collect([1, 2, 3, 45]),
            '_place_holder8_' => json_encode(['foo' => 'ba', 'key' => 'value']),
        ];

        $promptMessage = PromptMessage::make($this->faker->text(400), $replacers);

        $this->assertEquals(count($replacers), count($promptMessage->replacers));

        $this->assertIsString((string) $promptMessage);

        foreach ($replacers as $search => $replacer) {
            $this->assertIsString($promptMessage->placeholders[$search]);
            $this->assertEquals($replacer, $promptMessage->replacers[$search]);
        }
    }

    /** @test */
    public function itShouldReturnNewObjectWhenReplacePlaceholder()
    {
        $prompt_message = PromptMessage::make($this->faker->text(400));

        $this->assertNotSame($prompt_message, $replaced_prompt_message = $prompt_message->replace('_foo_', 'baa'));

        $this->assertNotSame($prompt_message, $replaced_many_prompt_message = $prompt_message->replaceMany(['_foo_' => 'baa']));

        $this->assertEquals($prompt_message->message, $replaced_prompt_message->promptMessage());

        $this->assertEquals($prompt_message->message, $replaced_many_prompt_message->promptMessage());
    }

    /** @test */
    public function itShouldGenerateStringMessage()
    {
        $prompt_message = new PromptMessage($this->faker->text(400).'_PLACEHOLDER_', ['_PLACEHOLDER_' => 'imagination']);

        $this->assertIsString((string) $prompt_message);

        $this->assertIsString($prompt_message->toString());

        $this->assertIsString($prompt_message->promptMessage());

        $this->assertIsString((string) PromptMessage::make($this->faker->text(400).'_PLACEHOLDER_', ['_PLACEHOLDER_' => 'imagination']));
    }
}
