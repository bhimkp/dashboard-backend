<!DOCTYPE html>
<html>

<head>
    <title>View Users Profile</title>
</head>

<body>
    <table>
        <tr>
            <td>Artist Id</td>
            <td>Artist Name</td>
        </tr>
        @foreach ($artists as $artist)
        <tr>
            <td>{{ $artist -> artist_id }}</td>
            <td>{{ $artist -> artist_name }}</td>
        </tr>
        @endforeach
    </table>
</body>

</html>
