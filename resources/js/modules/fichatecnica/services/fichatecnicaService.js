export async function getFichaTecnica(basePath) {

    const params = new URLSearchParams();

    if (basePath) {

        params.append(
            "basePath",
            basePath
        );

    }


    const response = await fetch(
        `/fichatecnica/buscar?${params.toString()}`
    );


    if (!response.ok) {

        throw new Error(
            `Error HTTP ${response.status}`
        );

    }


    const result =
        await response.json();


    console.log(
        "RESPUESTA FICHA:",
        result
    );


    return result.data || [];

}