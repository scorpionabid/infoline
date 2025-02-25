// school-config.js
export const SELECTORS = {
  form: "#schoolForm",
  modal: "#adminModal",
  adminForm: "#adminForm",
  adminSelect: "#adminSelect",
  createModal: "#createModal",
  addDataModal: "#addDataModal",
  editDataModal: "#editDataModal",
  selectAllCheckbox: "#select-all",
  schoolCheckbox: ".school-checkbox",
};

export const ENDPOINTS = {
  base: "/settings/personal/schools",
  assignAdmin: "/settings/personal/schools/{id}/assign-admin",
  removeAdmin: "/settings/personal/schools/{id}/remove-admin",
  availableAdmins: "/api/users/available-admins",
  deleteData: "/settings/personal/schools/data/{id}",
};

export const CONFIG = {
  select2Options: {
    theme: "bootstrap-5",
    width: "100%",
  },
  ajaxConfig: {
    delay: 250,
    minimumInputLength: 2,
    cache: true,
  },
};
