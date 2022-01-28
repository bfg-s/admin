module.exports = class extends Executor {

    static __name() {

        return "str";
    }

    to_slug(str, separator = '_') {

        if (typeof separator == 'undefined') separator = '-';

        let flip = separator === '-' ? '_' : '-';
        str = str.replace(flip, separator);

        return str.toLowerCase()
            .replace(new RegExp('\\s', 'g'), separator)
            .replace(new RegExp('\\s\\s', 'g'), separator)
            .replace(new RegExp('[' + separator + separator + ']+', 'g'), separator)
            .replace(new RegExp('[^a-z0-9' + separator + '\\s]', 'g'), '');
    }

    to_st(str, separator = '_') {
        return this.to_translit(this.to_slug(str, separator));
    }

    slug(set_to = null, separator = '_') {

        let str = this.translit();
        let result = this.to_slug(str);

        if (set_to) {

            $(set_to).val(result);
        } else {

            $(this.target).val(result);
        }

        return result;
    }

    to_translit(str) {

        let ru = {
            'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd',
            'е': 'e', 'ё': 'e', 'ж': 'j', 'з': 'z', 'и': 'i',
            'к': 'k', 'л': 'l', 'м': 'm', 'н': 'n', 'о': 'o',
            'п': 'p', 'р': 'r', 'с': 's', 'т': 't', 'у': 'u',
            'ф': 'f', 'х': 'h', 'ц': 'c', 'ч': 'ch', 'ш': 'sh',
            'щ': 'shch', 'ы': 'y', 'э': 'e', 'ю': 'u', 'я': 'ya'
        }, n_str = [];

        str = str.replace(/[ъь]+/g, '').replace(/й/g, 'i');

        for (var i = 0; i < str.length; ++i) {
            n_str.push(
                ru[str[i]]
                || ru[str[i].toLowerCase()] === undefined && str[i]
                || ru[str[i].toLowerCase()].replace(/^(.)/, (match) => {
                    return match.toUpperCase()
                })
            );
        }

        return n_str.join('');
    }

    translit(set_to = null) {

        let str = this.target.value;
        let result = this.to_translit(str);

        if (set_to) {

            $(set_to).val(result);
        }

        return result;
    }
};
