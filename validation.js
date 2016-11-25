var isClicked = false;

function validate(id)
{
	var span = document.getElementById(id.name);
	if (id.value == null || id.value == "")
	{
		switch(id.name)
		{
			case "username":
				span.innerHTML = "Username is required!";
				break;
			case "pwd":
				span.innerHTML = "Password is required!";
				break;
			case "fname":
				span.innerHTML = "First name is required!";
				break;
			case "lname":
				span.innerHTML = "Last name is required!";
				break;
			case "email":
				span.innerHTML = "E-mail is required!";
		}
		return false;
	}
	if(id.name == "email")
	{
		var re = /[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/;
		if(!re.test(id.value))
		{
			span.innerHTML = "Incorrect e-mail format!";
			return false;
		}
	}
	var c;
	var j = id.value.length;
	for(i = 0; i < j; i++)
	{
		c = id.value.charCodeAt(i);
		if (c < 48  || (c > 57 && c < 65) || (c > 90 && c < 97) || c > 122)
		{
			switch(id.name)
			{
				case "username":
					span.innerHTML = "Username can only have alphanumeric characters (A-Z and 0-9)";
					return false;
					break;
				case "pwd":
					span.innerHTML = "Password can only have alphanumeric characters (A-Z and 0-9)";
					return false;
			}
		}	
		if (c >= 48 && c <= 57 && (id.name=="fname" || id.name=="lname"))
			{
				switch(id.name)
				{
					case "fname":
						span.innerHTML = "First name cannot contain digits!";
						return false;
						break;
					case "lname":
						span.innerHTML = "Last name cannot contain digits!";
						return false;
				}
			}
	}
	span.innerHTML = "";
	return false;
}
function validate_final()
{
	var a = ["username", "pwd", "fname", "lname", "email"];
	for(i = 0; i < a.length; i++)
	{
		if(!validate(document.getElementById(a[i])))
			return false;
	}
	return true;
}
function transition(id)
{
	if(isClicked)
		return false;
	isClicked = true;
	var title = id.firstElementChild;
	var interval = setInterval(frame, 5);
	var i = 0;
	id.style.backgroundColor = 'rgb(0, 200, 0)';
	function frame()
	{
		if(i > 200)
		{
			clearInterval(interval);
			id.style.opacity = 1;
			id.style.backgroundColor = 'aqua';
			isClicked = false;
		}
		else
		{
			i++;
			id.style.backgroundColor = 'rgb(0,' + 200-i + ' ,' + i + ')';
			id.style.opacity = 1 - i/200;
			id.style.zIndex = 99 - i;
			title.style.top = -i*1.5 + 'px';
		}
	}
}
