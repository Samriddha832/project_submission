
let tostBox = document.getElementById('tostBox');
let sucessMsg = '<i class="fa-solid fa-circle-check"></i>Sucess';
let errorMsg = '<i class="fa-solid fa-xmark"></i>Error';
let invalidMsg = '<i class="fa-solid fa-exclamation"></i>Invalid';
function showTost(message, type = '') {
    let tostBox = document.getElementById('tostBox');
    let tost = document.createElement('div');
    tost.classList.add('tost');

    // Add icon based on type
    let icon = '';
    switch(type) {
        case 'error':
            tost.classList.add('error');
            icon = '<i class="fa-solid fa-xmark"></i> ';
            break;
        case 'invalid':
            tost.classList.add('invalid');
            icon = '<i class="fa-solid fa-exclamation"></i> ';
            break;
        case 'success':
            tost.classList.add('success');
            icon = '<i class="fa-solid fa-circle-check"></i> ';
            break;
        default:
            icon = '';
    }

    tost.innerHTML = icon + message;
    tostBox.appendChild(tost);

    setTimeout(() => {
        tost.remove();
    }, 5000);
}

