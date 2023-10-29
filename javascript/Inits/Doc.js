import * as clipboard from "clipboard-polyfill";

window.libs['doc::informed_pbcopy'] = function ($data) {
    clipboard.writeText($data).then(() => {
        exec("toast::success", "Copied to clipboard");
    });

    return $data;
};
