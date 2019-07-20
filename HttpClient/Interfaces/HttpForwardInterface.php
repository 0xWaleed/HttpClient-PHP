<?php


namespace HttpClient\Interfaces;


interface HttpForwardInterface
{
    public function getHeaders(): array;

    public function getCookies(): array;

    public function getUrl(): string;

    public function getBody(): HttpDataInterface;

    public function getMethod(): string;
}