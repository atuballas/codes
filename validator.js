/*
Form Validator Class
Created by: Alvin Mark Tuballas (atuballas@github.com)
December 12, 2012
https://github.com/atuballas/codes
*/

/*
Class Constructor
@Param:
field - field name
value - value of the field
type - type of validation
*/
function Validator( field, value, validations ){
	this.debug = true;
	this.field = field;
	this.value = value;
	this.validations = validations;
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
@Param: none
@Return: boolean(true/false)
*/
Validator.prototype.validate = function(){

	if( typeof(this.validations) == 'object' ){
		for(var key in this.validations){
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
				case 'valid':
					this.valid();
				break;
			}	
			
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
	if(this.kvalue==''){
		if(this.value==''){
			this.result=false;
			document.getElementById(this.field).className='inputerror';
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
	if(this.kvalue!=''){
		if(this.value.length<this.kvalue){
			this.result = false;
		}
	}else{
		console.log(this.lang[2]);
		this.result=false;
	}
}

/*
Function; minLength()
Checks if value of the field passes to a maximum length
@Param: none
@Return: boolean(true/false)
*/
Validator.prototype.maxLength = function(){
	if(this.kvalue!=''){
		if(this.value.length>this.kvalue){
			this.result = false;
		}
	}else{
		console.log(this.lang[3]);
		this.result=false;
	}
}

/*
Function; minLength()
Checks if value of the field is valid based on a regex passed
@Param: none
@Return: boolean(true/false)
*/
Validator.prototype.valid = function(){
	if(this.kvalue!=''){
		if(! this.kvalue.test(this.value)){
			this.result=false;
		}
	}else{
		console.log(this.lang[4]);
		this.result=false;
	}
}
