'use strict';

class AdminLte extends Executor {

    switch_search() {
        $( "#table_search_form" ).toggle( "fast" );
    }

    static __name () {
    
        return "lte";
    }
}

module.exports = AdminLte;
