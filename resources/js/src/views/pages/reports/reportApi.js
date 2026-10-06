import axios from "@/axios.js";
import i18n from "@/i18n/i18n";

const defaultError = () => i18n.t("report_error");


// The report list rarely changes: fetch it once per session and share it between the list and the viewer.
let typesRequest = null;
export function fetchReportTypes() {
    if (!typesRequest) {
        typesRequest = axios.get("/api/v1/report/types")
            .then((response) => response.data || [])
            .catch((error) => {
                typesRequest = null; // allow a retry
                throw error;
            });
    }
    return typesRequest;
}

// One page of a report as JSON for the on-screen table: { columns, rows, total, page, limit, summary }.
export async function fetchReportPage(type, filters = [], page = 1, limit = 25) {
    try {
        const response = await axios.post(`/api/v1/report/${type}/data`, { data: filters, page, limit });
        return response.data;
    } catch (error) {
        const data = error.response && error.response.data;
        throw (data && data.message) || defaultError();
    }
}

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
        let message = defaultError();
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
