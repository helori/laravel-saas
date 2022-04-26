import moment from 'moment'
import numeral from 'numeral'

numeral.register('locale', 'fr', {
    delimiters: {
        thousands: ' ',
        decimal: ','
    },
    abbreviations: {
        thousand: 'k',
        million: 'm',
        billion: 'b',
        trillion: 't'
    },
    ordinal : function (number) {
        return number === 1 ? 'er' : 'ème';
    },
    currency: {
        symbol: '€'
    }
});

numeral.locale('fr');


moment.locale('fr', {
    months : 'janvier_février_mars_avril_mai_juin_juillet_août_septembre_octobre_novembre_décembre'.split('_'),
});

export default {
    date(value, format, inputFormat) {
        if(!inputFormat){
            inputFormat = "YYYY-MM-DD HH:mm:ss";
        }
        if(value){
            return moment(value, inputFormat).format(format);
        }else{
            return '';
        }
    },
    age(value) {
        if(value){
            let birthday = moment(value, "YYYY-MM-DD HH:mm:ss");
            return moment().diff(birthday, 'years');
        }else{
            return '';
        }
    },
    phone(string) {
        if(typeof string === 'string' && string && string.length >= 12){
            return string.slice(0, 3) 
                + ' ' + string.slice(3, 4) 
                + ' ' + string.slice(4, 6) 
                + ' ' + string.slice(6, 8) 
                + ' ' + string.slice(8, 10) 
                + ' ' + string.slice(10);
        }
        return string;
    },
    number(value, decimals) {
        var format = '0,0';
        if(decimals > 0){
            format += '.';
            for(var i=0; i<parseInt(decimals); ++i){
                format += '0';
            }
        }
        if(value === null || value === '' || typeof value === 'undefined'){
            return '';
        }
        return numeral(value).format(format);
    },

    ucfirst(string) {
        if(string){
            return string.charAt(0).toUpperCase() + string.slice(1);
        }else{
            return '';
        }
    },

    strtoupper(string) {
        if(string){
            return string.toUpperCase();
        }else{
            return '';
        }
    },

    strtolower(string) {
        if(string){
            return string.toLowerCase();
        }else{
            return '';
        }
    },
}
