class SwalAlert {
    static warning(title, text) {
        Swal.fire({
            title: title,
            text: text,
            icon: "warning",
        });
    }

    static success(title, text) {
        Swal.fire({
            title: title,
            text: text,
            icon: "success",
        });
    }

    static error(title, text) {
        Swal.fire({
            title: title,
            text: text,
            icon: "error",
        });
    }

    static info(title, text) {
        Swal.fire({
            title: title,
            text: text,
            icon: "info",
        });
    }
}
