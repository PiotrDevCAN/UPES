/**
 *
 */

function checkIfEmailKnown() {
    var newEmail = $('#EMAIL_ADDRESS').val().trim().toLowerCase();
    var allreadyExists = ($.inArray(newEmail, knownEmail) >= 0);
    if (allreadyExists) { // comes back with Position in array(true) or false is it's NOT in the array.
        $('#savePerson').attr('disabled', true);
        $('#EMAIL_ADDRESS').css("background-color", "LightPink");
        alert('Person already defined to uPES. This does NOT mean they are PES Cleared for the account you want them to be cleared on. Simply, that they have been defined to uPES before.');
        return false;
    } else {
        $('#EMAIL_ADDRESS').css("background-color", "LightGreen");
        $('#savePerson').attr('disabled', false);
    }
}

export { checkIfEmailKnown as default };