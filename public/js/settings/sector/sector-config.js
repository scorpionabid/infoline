// sector-config.js
const SELECTORS = {
  form: "#sectorForm",
  modal: "#sectorModal",
  modalTitle: "#sectorModalLabel",
  submitBtn: "#submitBtn",
  nameInput: "#name",
  regionSelect: "#region_id",
  idInput: "#sectorId",
  adminModal: "#adminModal",
  adminForm: "#adminForm",
  adminSelect: "#adminSelect",
};

const ENDPOINTS = {
  base: "/settings/personal/sectors",
  data: "/settings/personal/sectors/data",
  cleanupDeleted: "/settings/personal/sectors/cleanup-deleted",
  assignAdmin: "/settings/personal/sectors/{id}/assign-admin",
  removeAdmin: "/settings/personal/sectors/{id}/remove-admin",
};

const CONFIG = {
  selectOptions: {
    theme: "bootstrap-5",
    width: "100%",
  },
};
