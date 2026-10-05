import axios from "@/axios.js";

const DEFAULT_ERROR = "មិនអាចបង្កើតរបាយការណ៍បានទេ សូមព្យាយាមម្តងទៀត។";

// Fetch a report PDF. Resolves { blob, filename }; rejects with a readable message.
export async function fetchReport(type, filters = [], download = false) {
    try {
        const response = await axios.post(
            `/api/v1/report/${type}` + (download ? "?download=1" : ""),
            { data: filters },
            { responseType: "blob" }
        );
        const disposition = response.headers["content-disposition"] || "";
        const match = disposition.match(/filename="?([^";]+)"?/);
        return {
            blob: new Blob([response.data], { type: "application/pdf" }),
            filename: match ? match[1] : `${type}-report.pdf`,
        };
    } catch (error) {
        // Error bodies arrive as a Blob because of responseType: "blob".
        let message = DEFAULT_ERROR;
        const data = error.response && error.response.data;
        if (data instanceof Blob) {
            try {
                message = JSON.parse(await data.text()).message || message;
            } catch (e) {
                // keep the default message
            }
        }
        throw message;
    }
}

export function saveBlob(blob, filename) {
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.href = url;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    link.remove();
    URL.revokeObjectURL(url);
}

export async function downloadReport(type, filters = []) {
    const { blob, filename } = await fetchReport(type, filters, true);
    saveBlob(blob, filename);
}
