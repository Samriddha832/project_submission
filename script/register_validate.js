document.addEventListener("DOMContentLoaded", function () {

    function ValidateForm(formId) {
        const form = document.getElementById(formId);
        if (!form) return;

        form.addEventListener("submit", function (event) {

            const password = form.querySelector('#password').value;
            const confirmPassword = form.querySelector('#confirm_password').value;
            const phone = form.querySelector('#phone');
            const errorDiv = form.querySelector('#confirm_password_error');

            errorDiv.textContent = '';

            if (password !== confirmPassword) {
                errorDiv.textContent = 'Password does not match With Previous Password';
                event.preventDefault(); 
                return;
            }

			const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
            if (!passwordPattern.test(password)) {
                errorDiv.textContent =
                    'Week Password At leat one uppercase, lowercase, speicla symbol ';
                event.preventDefault(); 
                return;
            }

            if(phone){
                const phonepattern = /^9[78][0-9]{8}$/;
                if(!phonepattern.test(phone.value)){
                    const errorphone = form.querySelector('.phone_validate_error');
                    errorphone.textContent = 'js';
                }
            }
        });
    }

    ValidateForm('Change_Password');
    ValidateForm('registerForm'); // only if it exists
});
