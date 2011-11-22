<?php
namespace Fp\OpenIdBundle\Consumer;

class ConsumerProvider
{
    protected $consumers = array();

    protected $defaultConsumer;

    public function addConsumer(ConsumerInterface $consumer)
    {
        $this->consumers[] = $consumer;
    }

    public function setDefault(ConsumerInterface $consumer)
    {
        $this->defaultConsumer = $consumer;
    }

    /**
     * @throws \InvalidArgumentException
     *
     * @param string $identifier
     *
     * @return \Fp\OpenIdBundle\Consumer\ConsumerInterface
     */
    public function provide($identifier)
    {
        foreach ($this->consumers as $consumer) {
            if ($consumer->supports($identifier)) {
                return $consumer;
            }
        }

        if ($this->defaultConsumer && $this->defaultConsumer->supports($identifier)) {
            return $this->defaultConsumer;
        }

        throw new \InvalidArgumentException(sprintf('Cannot provide consumer for the identifier: %s', $identifier));
    }
}