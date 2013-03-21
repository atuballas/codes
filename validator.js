/*
Form Validator Class
Created by: Alvin Mark Tuballas (atuballas@github.com)
December 12, 2012
https://github.com/atuballas/codes
*/

/*
Class Constructor
@Param: none
*/
function Validator(  ){

	this.field = '';
	this.value = '';
	this.validations = '';

	this.debug = true;
	this.result = true;
	this.lang = new Array(
											'Validation Error: 3rd parameter expects an object.',
											'Validation Error: invalid parameter passed for required() function',
											'Validation Error: invalid parameter passed for minLength() function',
											'Validation Error: invalid parameter passed for maxLength() function',
											'Validation Error: invalid parameter passed for valid() function'
									   );
}

/*
Function; validate()
Generic function that calls/executes individual validation type
@Param:
field - field name
value - value of the field
type - type of validation
@Return: boolean(true/false)
*/
Validator.prototype.validate = function( field, value, validations, errelement, errtexts ){

	this.field = field;
	this.value = value;
	this.validations = validations;
	this.errelement = errelement;
	this.errtexts = errtexts;
	
	if( typeof(this.validations) == 'object' ){
		for(var key in this.validations){
			if(this.result){
				if(!this.validations.hasOwnProperty(key)){
					continue;
				}

				this.key = key;
				this.kvalue = this.validations[key];

				switch( key ){
					case 'required':
						this.required();
					break;
					case 'minLength':
						this.minLength();
					break;
					case 'maxLength':
						this.maxLength();
					break;
					case 'pattern':
						this.pattern();
					break;
				}	
			}
		}
		if(this.validations.length>1){
			this.result = true;
		}
	}else{
		if(this.debug) console.log(this.lang[0]);
		return false;
	}
}

/*
Function; required()
Checks if value of field is not empty and must have a value
@Param: none
@Return: boolean(true/false)
*/
Validator.prototype.required = function(){
	this.defaultElement();
	if(this.kvalue==''){
		if(this.value==''){
			this.result=false;
			if(!this.hasClass(document.getElementById(this.field),'inputerror')) document.getElementById(this.field).className+=' inputerror';
			document.getElementById(this.errelement).innerHTML = this.errtexts['required'];
		}
	}else{
		console.log(this.lang[1]);
		this.result=false;
	}
}

/*
Function; minLength()
Checks if value of the field passes to a minimum length
@Param: none
@Return: boolean(true/false)
*/
Validator.prototype.minLength = function(){
	this.defaultElement();
	if(this.kvalue!=''){
		if(this.value.length<this.kvalue){
			this.result = false;
			if(!this.hasClass(document.getElementById(this.field),'inputerror')) document.getElementById(this.field).className+=' inputerror';
			document.getElementById(this.errelement).innerHTML = this.errtexts['minLength'];
		}
	}else{
		console.log(this.lang[2]);
		this.result=false;
	}
}

/*
Function; maxLength()
Checks if value of the field passes to a maximum length
@Param: none
@Return: boolean(true/false)
*/
Validator.prototype.maxLength = function(){
	this.defaultElement();
	if(this.kvalue!=''){
		if(this.value.length>this.kvalue){
			this.result = false;
			if(!this.hasClass(document.getElementById(this.field),'inputerror')) document.getElementById(this.field).className+=' inputerror';
			document.getElementById(this.errelement).innerHTML = this.errtexts['maxLength'];
		}
	}else{
		console.log(this.lang[3]);
		this.result=false;
	}
}

/*
Function; valid()
Checks if value of the field is valid based on a regex passed
@Param: none
@Return: boolean(true/false)
*/
Validator.prototype.pattern = function(){
	this.defaultElement();
	if(this.kvalue!=''){
		if(! this.kvalue.test(this.value)){
			this.result=false;
			if(!this.hasClass(document.getElementById(this.field),'inputerror')) document.getElementById(this.field).className+=' inputerror';
			document.getElementById(this.errelement).innerHTML = this.errtexts['pattern'];	
		}
	}else{
		console.log(this.lang[4]);
		this.result=false;
	}
}

Validator.prototype.hasClass = function(e,c){
	var r = new RegExp('\\b' + c + '\\b');
    return r.test(e.className);
}

Validator.prototype.defaultElement = function(){
	document.getElementById(this.errelement).innerHTML = '';
	document.getElementById(this.field).className = document.getElementById(this.field).className.replace( /(?:^|\s)inputerror(?!\S)/g , '' )
}
