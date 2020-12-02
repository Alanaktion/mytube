<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/" xmlns:moz="http://www.mozilla.org/2006/browser/search/">
    <ShortName>{{ config('app.name') }}</ShortName>
    <Description>{{ __('Search :name', ['name' => config('app.name')]) }}</Description>
    <InputEncoding>UTF-8</InputEncoding>
    <Image width="16" height="16" type="image/x-icon">{{ url('/favicon.ico') }}</Image>
    <Url type="text/html" template="{{ url('/search') }}">
        <Param name="q" value="{searchTerms}"/>
    </Url>
    <Url type="application/opensearchdescription+xml"
        rel="self"
        template="{{ url('/opensearch.xml') }}"/>
    <moz:SearchForm>{{ url('/') }}</moz:SearchForm>
</OpenSearchDescription>
