<?php

namespace App\Domain\User\ValueObject;

use Immutable\ValueObject\ValueObject;

final class NotEmpty extends ValueObject
{
    protected string $input = '';

    /**
     * @inheritDoc
     *
     * @throws \Immutable\Exception\ImmutableObjectException
     * @throws \Immutable\Exception\InvalidValueException
     */
    public function __construct(string $input)
    {
        $this->withChanged($input);
        parent::__construct();
    }

    /**
     * @param string $input
     *
     * @return ValueObject
     * @throws \Immutable\Exception\ImmutableObjectException
     * @throws \Immutable\Exception\InvalidValueException
     */
    public function withChanged(string $input): ValueObject
    {
        $this->assertValid($input);
        return $this->with([
            'input' => $input,
        ]);
    }

    public function getInput() : string
    {
        return $this->input;
    }

    /**
     * @param string $data
     *
     * @throws \Immutable\Exception\InvalidValueException
     */
    protected function assertValid(string $data) : void
    {
        if (empty($data)) {
            $this->throwInvalidValueException(
                '$input',
                'is empty',
                $data
            );
        }
    }
}
