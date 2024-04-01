<html>
<body>
<form action="ajax/confirm_booking.php" method="post">
    <div class="col-md-6 mb-3">
        <label class="form-label">Check-in</label>
        <input class="form-control shadow-none checkin"onchange="check_availability()" type="date" name="check_in" id="checkin" required>
    </div>
    <div class="col-md-6 mb-4">
        <label class="form-label">Check-out</label>
        <input class="form-control shadow-none checkout"onchange="check_availability()" type="date" name="check_out" id="checkout" required>
    </div>
    <button name="check_availability" class="btn w-100 text-white custom-bg shadow-none mb-1">Pay Now</button>
</form>
<?php
if (function_exists('gd_info')) {
    echo "GD is enabled";
} else {
    echo "GD is not enabled";
}
?>


</body>
</html>