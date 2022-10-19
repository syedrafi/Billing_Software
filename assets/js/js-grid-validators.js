

jsGrid.validators.floatwithnull = {
    message: "Please enter a valid number",
    validator: function (value, item) {
        if (value == "") {
            return true;
        }
        if ($.isNumeric(value)) {
            return true;
        }
        return false;
    }
}
jsGrid.validators.email = {
    message: "Please enter a Valid Email",
    validator: function (value, item) {
        if (value == "") {
            return true;
        }
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(value);

    }
}
jsGrid.validators.mobile = {
    message: "Please enter 10 digit valid Mobile No",
    validator: function (value, item) {
        if (value == "") {
            return true;
        }
        alert(value.length);
      if(value.length==10){
          return true;
      }
      return false;

    }
}

