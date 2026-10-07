<template>
  <td ref="td" class="td vs-table--td">
    <span><slot /></span>
  </td>
</template>

<script>
export default {
  name: 'VsTd',
  props: {
    data: { default: null },
  },
}
</script>
