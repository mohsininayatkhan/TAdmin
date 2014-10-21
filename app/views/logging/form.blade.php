<form action="{{ $_SERVER['REQUEST_URI'] }}" method="post">
    <label>Date</label>
    <input type="date" name="date" value="{{ $today }}"/>
    <input type="submit" value="submit"/>
</form>