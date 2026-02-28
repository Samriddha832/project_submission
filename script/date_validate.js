const checkInInputs = document.querySelectorAll('.check_in');

checkInInputs.forEach((check_in) => {
    const check_out = check_in.closest('form').querySelector('.check_out');

    // Today's date
    const today = new Date();
    const todayStr = today.toISOString().split('T')[0];

    // Set min for check-in
    check_in.min = todayStr;
    if (!check_in.value || check_in.value < todayStr) check_in.value = todayStr;

    // Set min for checkout = check-in +1
    const minCheckoutDate = new Date(check_in.value);
    minCheckoutDate.setDate(minCheckoutDate.getDate() + 1);
    const minCheckoutStr = minCheckoutDate.toISOString().split('T')[0];

    check_out.min = minCheckoutStr;
    if (!check_out.value || check_out.value <= check_in.value) {
        check_out.value = minCheckoutStr;
    }

    // Update checkout when check-in changes
    check_in.addEventListener('change', function() {
        const newCheckIn = new Date(this.value);
        newCheckIn.setDate(newCheckIn.getDate() + 1);
        const newMinCheckoutStr = newCheckIn.toISOString().split('T')[0];

        check_out.min = newMinCheckoutStr;
        if (!check_out.value || check_out.value <= this.value) {
            check_out.value = newMinCheckoutStr;
        }
    });
});
