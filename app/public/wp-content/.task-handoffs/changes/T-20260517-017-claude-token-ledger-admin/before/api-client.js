// -------------------------------------------------------------

// PiBackend API client ? thin fetch wrapper, JWT auth, typed errors.

// Used by both admin and user dashboards.

// -------------------------------------------------------------



/**

 * Ensures a promise takes at least `ms` milliseconds.

 * Useful for making skeleton loading states visible on fast connections.

 */

export async function withDelay(promise, ms = 1000) {

  const [res] = await Promise.all([

    promise,

    new Promise((resolve) => setTimeout(resolve, ms)),

  ]);

  return res;

}



const BASE_URL = import.meta.env.VITE_PI_API_URL || import.meta.env.VITE_API_URL || "http://localhost:8000";

const IS_DEV = import.meta.env.DEV;



class PiApiError extends Error {

  constructor(message, { status = 0, code = "unknown", data = null } = {}) {

    super(message);

    this.name = "PiApiError";

    this.status = status;

    this.code = code;

    this.data = data;

  }

}



function readAuth() {

  // JWT for user/admin UI auth (not the license Bearer key)

  return localStorage.getItem("pi_jwt") || "";

}



function readLicense() {

  // License key for direct plugin-style calls (/v1/ai/*, /v1/license/*)

  return localStorage.getItem("pi_license_key") || "";

}



async function request(method, path, { body, params, license, jwt, raw } = {}) {

  // Auto-prefix /v1/ if missing and not an absolute URL

  let finalPath = path;

  if (!path.startsWith("http") && !path.startsWith("/v1")) {

    finalPath = `/v1${path.startsWith("/") ? "" : "/"}${path}`;

  }

  

  const url = new URL(finalPath.startsWith("http") ? finalPath : BASE_URL + finalPath);

  if (params) {

    for (const [k, v] of Object.entries(params)) {

      if (v !== undefined && v !== null && v !== "") url.searchParams.set(k, v);

    }

  }



  const headers = { Accept: "application/json" };

  if (body) headers["Content-Type"] = "application/json";



  // Dual auth: JWT for dashboard, license key for direct API

  const token = jwt !== undefined ? jwt : readAuth();

  const IS_DEV = import.meta.env.DEV;

  const licKey = license !== undefined ? license : readLicense();

  if (token) headers["Authorization"] = `Bearer ${token}`;

  else if (licKey) headers["Authorization"] = `Bearer ${licKey}`;



  let response;

  try {

    response = await fetch(url.toString(), {

      method,

      headers,

      body: body ? JSON.stringify(body) : undefined,

      credentials: "omit",

    });

  } catch (e) {

    throw new PiApiError(`Network error: ${e.message}`, { code: "network_error" });

  }



  if (raw) return response;



  let data = null;

  const text = await response.text();

  if (text) {

    try {

      data = JSON.parse(text);

    } catch {

      data = { raw: text };

    }

  }



  if (!response.ok) {

    throw new PiApiError(

      data?.message || `HTTP ${response.status}`,

      {

        status: response.status,

        code: data?.code || `http_${response.status}`,

        data,

      }

    );

  }



  // Vjppro Response Unwrapping: If backend returned { success, data, ... }, return the data part.

  if (data && typeof data === "object" && "success" in data && "data" in data) {

    return data.data;

  }



  return data;

}



// --- High-level API surface organised by domain --------------



export const api = {

  // Auth (dashboard JWT ? requires backend endpoints added in Phase 3)

  auth: {

    login: (email, password) => request("POST", "/v1/auth/login", { body: { email, password } }),

    signup: (payload) => request("POST", "/v1/auth/signup", { body: payload }),

    me: () => request("GET", "/v1/auth/me"),

    logout: () => {

      localStorage.removeItem("pi_jwt");

      localStorage.removeItem("pi_admin");

    },

  },



  // License (works with license Bearer key)

  license: {

    verify: (payload) => request("POST", "/v1/license/verify", { body: payload }),

    activate: (payload) => request("POST", "/v1/license/activate", { body: payload }),

    deactivate: (payload) => request("POST", "/v1/license/deactivate", { body: payload }),

    stats: () => request("GET", "/v1/license/stats"),

  },



  // Pi AI Cloud ? wallet, ledger, packs, Stripe

  ai: {

    wallet: () => request("GET", "/v1/ai/wallet"),

    ledger: (limit = 50, offset = 0) =>

      request("GET", "/v1/ai/ledger", { params: { limit, offset } }),

    packs: () => request("GET", "/v1/ai/topup/packs"),

    checkout: (pack, successUrl, cancelUrl) =>

      request("POST", "/v1/ai/topup/checkout", {

        body: { pack, success_url: successUrl, cancel_url: cancelUrl },

      }),

    complete: (payload) => request("POST", "/v1/ai/complete", { body: payload }),

  },



  billing: {

    subscribeCheckout: (payload) =>

      request("POST", "/v1/billing/subscribe/checkout", { body: payload }),

    changeTier: (new_tier) =>

      request("PATCH", "/v1/billing/subscribe/change-tier", { body: { new_tier } }),

    cancel: () => request("POST", "/v1/billing/subscribe/cancel"),

    status: () => request("GET", "/v1/billing/subscribe/status"),

    simulateSuccess: (payload) =>

      request("POST", "/v1/billing/subscribe/simulate-success", { body: payload }),

  },



  // Updates server

  updates: {

    check: (plugin, current) =>

      request("GET", `/v1/updates/check/${plugin}`, { params: { current } }),

  },



  // Admin endpoints (require admin JWT ? to be added backend-side)

  admin: {

    overview: () => request("GET", "/v1/admin/overview"),

    licenses: (params) => request("GET", "/v1/admin/licenses", { params }),

    createLicense: (payload) => request("POST", "/v1/admin/licenses", { body: payload }),

    updateLicense: (id, payload) =>

      request("PATCH", `/v1/admin/licenses/${id}`, { body: payload }),

    revokeLicense: (id) => request("POST", `/v1/admin/licenses/${id}/revoke`),

    reactivateLicense: (id) => request("POST", `/v1/admin/licenses/${id}/reactivate`),

    deleteLicense: (id) => request("DELETE", `/v1/admin/licenses/${id}`),

    users: (params) => request("GET", "/v1/admin/users", { params }),

    getUser: (id) => request("GET", `/v1/admin/users/${id}`),

    updateUserProfile: (id, payload) =>

      request("PATCH", `/v1/admin/users/${id}/profile`, { body: payload }),

    providers: () => request("GET", "/v1/admin/providers"),

    toggleProvider: (id, enabled) =>

      request("PATCH", `/v1/admin/providers/${id}`, { body: { is_enabled: enabled } }),

    updateProvider: (id, payload) =>

      request("PATCH", `/v1/admin/providers/${id}`, { body: payload }),

    createProvider: (payload) =>

      request("POST", "/v1/admin/providers", { body: payload }),

    deleteProvider: (id) =>

      request("DELETE", `/v1/admin/providers/${id}`),

    testProvider: (id) =>

      request("POST", `/v1/admin/providers/${id}/test`),

    usage: (params) => request("GET", "/v1/admin/usage", { params }),

    revenue: (params) => request("GET", "/v1/admin/revenue", { params }),

    releases: () => request("GET", "/v1/admin/releases"),

    uploadRelease: (formData) =>

      request("POST", "/v1/admin/releases", { body: formData, raw: true }),

    adjustTokens: (licenseId, delta, note) =>

      request("POST", `/v1/admin/licenses/${licenseId}/tokens`, {

        body: { delta, note },

      }),

    getSettings: () => request("GET", "/v1/admin/settings"),

    updateSettings: (payload) => request("PUT", "/v1/admin/settings", { body: payload }),



    // Keys pool

    keysSummary: () => request("GET", "/v1/admin/keys/summary"),

    keys: (params) => request("GET", "/v1/admin/keys", { params }),

    createKey: (payload) => request("POST", "/v1/admin/keys", { body: payload }),

    bulkImportKeys: (rows) => request("POST", "/v1/admin/keys/bulk", { body: { rows } }),

    updateKey: (id, payload) => request("PATCH", `/v1/admin/keys/${id}`, { body: payload }),

    deleteKey: (id) => request("DELETE", `/v1/admin/keys/${id}`),

    allocateKeys: (payload) => request("POST", "/v1/admin/keys/allocate", { body: payload }),

    revokeKey: (id) => request("POST", `/v1/admin/keys/${id}/revoke`),

    revealKey: (id) => request("GET", `/v1/admin/keys/${id}/reveal`),

    resetKeyPeriod: () => request("POST", "/v1/admin/keys/reset-period"),

    // Bulk release license keys back to shared pool (T-20260513-001)
    releaseLicenseKeys: (licenseId) =>
      request("POST", `/v1/admin/licenses/${licenseId}/keys/release-all`),



    // Packages

    packages: () => request("GET", "/v1/admin/packages"),

    createPackage: (payload) => request("POST", "/v1/admin/packages", { body: payload }),

    updatePackage: (slug, payload) => request("PATCH", `/v1/admin/packages/${slug}`, { body: payload }),

    deletePackage: (slug) => request("DELETE", `/v1/admin/packages/${slug}`),



    // License  Package

    getLicensePackage: (id) => request("GET", `/v1/admin/licenses/${id}/package`),

    assignPackage: (id, payload) => request("POST", `/v1/admin/licenses/${id}/package`, { body: payload }),

    resetLicensePeriod: (id) => request("POST", `/v1/admin/licenses/${id}/package/reset-period`),



    // Audit log

    auditLog: (params) => request("GET", "/v1/admin/audit-log", { params }),



    // Cron jobs

    cronStatus: () => request("GET", "/v1/admin/cron"),

    runCron: (slug) => request("POST", `/v1/admin/cron/${slug}/run`),

  },



  // Customer-facing Pi AI Cloud

  cloud: {

    myPackage: () => request("GET", "/v1/cloud/package"),

    myUsage: (days = 30) => request("GET", "/v1/cloud/usage", { params: { days } }),

  },



  // Public (no auth) ? marketing pages

  public: {

    packages: () => request("GET", "/v1/public/packages"),

  },

};



export { PiApiError, readAuth, readLicense, BASE_URL };

export default api;

