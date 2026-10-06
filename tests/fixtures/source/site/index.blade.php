@php($title = "Home")
@extends("layout")

@section("title", $title . " — " . $site['name'])

@section("content")
<article>
    <h2>{{ $title }}</h2>
    <p>{{ $site['description'] }}</p>
</article>
