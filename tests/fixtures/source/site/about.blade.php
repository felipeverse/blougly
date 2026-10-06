@php($title = "About")
@extends("layout")

@section("title", $title . " — " . $site['name'])

@section("content")
<article>
    <h2>{{ $title }}</h2>
    <p>About this fixture site.</p>
</article>
