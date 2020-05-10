module.exports = class extends Executor {

    slug (set_to = null, separator = '_') {

        let str = this.translit();

        if(typeof separator == 'undefined') separator = '-';

        let flip = separator === '-' ? '_' : '-';
        str = str.replace(flip, separator);

        str = str.toLowerCase()
            .replace(new RegExp('[^a-z0-9' + separator + '\\s]', 'g'), '');

        str = str.replace(new RegExp('[' + separator + '\\s]+', 'g'), separator);

        let result = str.replace(new RegExp('^[' + separator + '\\s]+|[' + separator + '\\s]+$', 'g'),'');

        if (set_to) {

            $(set_to).val(result);
        }

        else {

            $(this.target).val(result);
        }

        return result;
    }

    translit (set_to = null) {

        let str = this.target.value;

        let ru = {
            'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd',
            'е': 'e', 'ё': 'e', 'ж': 'j', 'з': 'z', 'и': 'i',
            'к': 'k', 'л': 'l', 'м': 'm', 'н': 'n', 'о': 'o',
            'п': 'p', 'р': 'r', 'с': 's', 'т': 't', 'у': 'u',
            'ф': 'f', 'х': 'h', 'ц': 'c', 'ч': 'ch', 'ш': 'sh',
            'щ': 'shch', 'ы': 'y', 'э': 'e', 'ю': 'u', 'я': 'ya'
        }, n_str = [];

        str = str.replace(/[ъь]+/g, '').replace(/й/g, 'i');

        for ( var i = 0; i < str.length; ++i ) {
            n_str.push(
                ru[ str[i] ]
                || ru[ str[i].toLowerCase() ] === undefined && str[i]
                || ru[ str[i].toLowerCase() ].replace(/^(.)/, ( match ) => { return match.toUpperCase() })
            );
        }

        let result = n_str.join('');

        if (set_to) {

            $(set_to).val(result);
        }

        return result;
    }

    static __name () {
    
        return "str";
    }
};