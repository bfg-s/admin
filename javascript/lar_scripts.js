/**
 * Here the scripts will be executed every time the
 * page is loaded and the location state changes.
 * @param $root
 * @param $methods
 */
module.exports = ($root, $methods) => {

    if ($root[0] !== document) {

        $root.find('button[role="iconpicker"],div[role="iconpicker"]').iconpicker();
    }
};
