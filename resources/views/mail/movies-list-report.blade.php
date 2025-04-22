<html>
    Hello this is the report of the movies list:

    @foreach ($movies as $movie)
        <p>{{ $movie->title }}</p>
        <p>{{ $movie->year }}</p>
    @endforeach
</html>