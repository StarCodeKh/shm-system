/*=========================================================================================
  File Name: moduleCalendarMutations.js
  Description: Calendar Module Mutations
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/


export default {
  ADD_ITEM(state, item) {
    state.products.unshift(item)
  },
  SET_PRODUCTS(state, products) {
    state.products = products
  },
  // SET_LABELS(state, labels) {
  //   state.eventLabels = labels
  // },
  UPDATE_PRODUCT(state, product) {
    console.log("here is sample update data");
      const productIndex = state.products.findIndex((p) => p.id == product.id)
      Object.assign(state.products[productIndex], product)
  },
  REMOVE_ITEM(state, itemId) {
      const ItemIndex = state.products.findIndex((p) => p.id == itemId)
      state.products.splice(ItemIndex, 1)
  },
  // Data
  SET_DATA(state, dataObjects) {
    state.dataObjects = dataObjects
  },
  ADD_DATA(state, item) {
    state.dataObjects.unshift(item)
  },
  UPDATE_DATA(state, dataObj) {
    const DataIndex = state.dataObjects.findIndex((obj) => obj.id == dataObj.id)
    Object.assign(state.dataObjects[DataIndex], dataObj)
  },
  REMOVE_DATA(state, itemId) {
    const ItemIndex = state.dataObjects.findIndex((obj) => obj.id == itemId)
    state.dataObjects.splice(ItemIndex, 1)
  },
}
