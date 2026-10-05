<template>
  <div class="d-file-picker" :class="{ 'has-file': !!value }">
    <div class="d-file-picker__box" role="button" tabindex="0" :title="value ? $t('upload_change') : $t('upload_choose')"
      @click="$emit('browse')" @keydown.enter.prevent="$emit('browse')" @keydown.space.prevent="$emit('browse')">

      <!-- Thumbnail: image preview, file-type badge, or upload icon when empty -->
      <span class="d-file-picker__thumb" :class="{ 'is-preview': !!value }" :title="value ? $t('upload_view') : null"
        @click="onThumb">
        <img v-if="value && isImage && !imageFailed" :src="value" alt="" @error="imageFailed = true">
        <span v-else-if="value" class="d-file-picker__type" :style="{ '--type': typeInfo.color }">
          <feather-icon :icon="typeInfo.icon" svgClasses="h-3 w-3" />
          <small>{{ extension || 'FILE' }}</small>
        </span>
        <feather-icon v-else icon="UploadIcon" svgClasses="h-4 w-4" />
        <span v-if="value" class="d-file-picker__zoom">
          <feather-icon icon="EyeIcon" svgClasses="h-4 w-4" />
        </span>
      </span>

      <span class="d-file-picker__text">
        <span class="d-file-picker__title" :title="value ? fileName : null">{{ value ? fileName : $t('upload_choose') }}</span>
        <span v-if="value" class="d-file-picker__hint is-done">
          <feather-icon icon="CheckCircleIcon" svgClasses="h-3 w-3" />
          {{ $t('upload_selected') }}
        </span>
        <span v-else class="d-file-picker__hint">{{ hintText }}</span>
      </span>

      <span class="d-file-picker__browse">{{ value ? $t('upload_change') : $t('upload_browse') }}</span>
      <button v-if="value" type="button" class="d-file-picker__remove" :title="$t('upload_remove')"
        @click.stop="$emit('clear')">
        <feather-icon icon="XIcon" svgClasses="h-4 w-4" />
      </button>
    </div>
  </div>
</template>

<script>
// Picker shell for a file field. Picking itself happens in the file manager:
// the parent opens it on `browse`, and passes the chosen path back as `value`.
const IMAGE_EXT = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'bmp']
const IMAGE_FIELDS = /logo|photo|image|thumbnail|avatar|icon/i
const TYPES = {
  pdf: { icon: 'FileTextIcon', color: '220, 53, 69' },
  doc: { icon: 'FileTextIcon', color: '43, 87, 154' },
  docx: { icon: 'FileTextIcon', color: '43, 87, 154' },
  xls: { icon: 'GridIcon', color: '33, 115, 70' },
  xlsx: { icon: 'GridIcon', color: '33, 115, 70' },
  csv: { icon: 'GridIcon', color: '33, 115, 70' },
}

export default {
  name: 'd-file-picker',
  props: {
    value: { type: String, default: '' },
    name: { type: String, default: '' }, // field name, used to guess whether an image is expected
    hint: { type: String, default: '' },
  },
  data: () => ({
    imageFailed: false,
  }),
  computed: {
    extension() {
      const clean = (this.value || '').split(/[?#]/)[0]
      const dot = clean.lastIndexOf('.')
      return dot > -1 ? clean.slice(dot + 1).toLowerCase() : ''
    },
    isImage() {
      return IMAGE_EXT.includes(this.extension) || (!this.extension && IMAGE_FIELDS.test(this.name))
    },
    fileName() {
      const clean = (this.value || '').split(/[?#]/)[0]
      return decodeURIComponent(clean.split('/').pop() || clean)
    },
    typeInfo() {
      return TYPES[this.extension] || { icon: 'FileIcon', color: '92, 99, 120' }
    },
    hintText() {
      if (this.hint) return this.hint
      return this.$t(IMAGE_FIELDS.test(this.name) ? 'upload_hint_image' : 'upload_hint_file')
    },
  },
  watch: {
    value() {
      this.imageFailed = false
    },
  },
  methods: {
    // Clicking the thumbnail of a chosen file previews it instead of re-opening the picker
    onThumb(event) {
      if (!this.value) return
      event.stopPropagation()
      if (this.$listeners.view) this.$emit('view', this.value)
      else window.open(this.value, '_blank', 'noopener')
    },
  },
}
</script>

<style lang="scss">
.d-file-picker {
  --picker-accent: var(--vs-primary);
  width: 100%;

  &__box {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    height: var(--input-height, 42px);
    padding: 0 6px 0 6px;
    text-align: left;
    border: 1px dashed #c9ccd6;
    border-radius: var(--input-radius, 10px);
    background: #fff;
    cursor: pointer;
    transition: border-color .2s ease, background .2s ease, box-shadow .2s ease;

    &:hover {
      border-color: rgb(var(--picker-accent));
      background: rgba(var(--picker-accent), .03);

      .d-file-picker__thumb:not(.is-preview) {
        color: #fff;
        background: rgb(var(--picker-accent));

        svg {
          animation: d-file-picker-bounce .6s ease;
        }
      }

      .d-file-picker__browse {
        background: rgba(var(--picker-accent), .1);
      }
    }

    &:focus-visible {
      outline: none;
      border-color: rgb(var(--picker-accent));
      box-shadow: 0 0 0 3px rgba(var(--picker-accent), .2);
    }
  }

  // Empty: the title stays whole and the hint gives way; chosen: the file name gives way
  &.has-file &__title {
    flex-shrink: 1;
  }

  &.has-file &__hint {
    flex-shrink: 0;
  }

  &.has-file &__box {
    border-style: solid;
    border-color: var(--input-border, #d8d9e3);
  }

  // ---------------------------------------------------------------- Thumbnail
  &__thumb {
    position: relative;
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: 7px;
    color: #5f6170;
    background: #f3f4f6;
    overflow: hidden;
    transition: background .2s ease, color .2s ease;

    .feather-icon {
      display: flex;
    }

    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    &.is-preview {
      cursor: zoom-in;

      &:hover .d-file-picker__zoom {
        opacity: 1;
      }
    }
  }

  &__zoom {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    background: rgba(23, 23, 74, .45);
    opacity: 0;
    transition: opacity .2s ease;
  }

  &__type {
    --type: 92, 99, 120;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    color: rgb(var(--type));
    background: rgba(var(--type), .1);

    small {
      font-size: .5rem;
      font-weight: 700;
      line-height: 1.1;
      letter-spacing: .03em;
      text-transform: uppercase;
    }
  }

  // ---------------------------------------------------------------- Text
  &__text {
    display: flex;
    align-items: baseline;
    flex-wrap: nowrap;
    gap: 8px;
    overflow: hidden;
    flex: 1;
    min-width: 0;
  }

  &__title {
    flex-shrink: 0;
    min-width: 0;
    font-size: 14px;
    font-weight: 600;
    color: #2c2e45;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  &__hint {
    display: inline-flex;
    align-items: center;
    flex-shrink: 1;
    min-width: 0;
    gap: 4px;
    font-size: 12px;
    color: #8a8ca5;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;

    // Plain text hint: block so the ellipsis applies
    &:not(.is-done) {
      display: block;
    }

    &.is-done {
      color: rgb(var(--picker-accent));
    }

    .feather-icon {
      display: flex;
    }
  }

  // ---------------------------------------------------------------- Actions
  &__browse {
    flex-shrink: 0;
    padding: 5px 10px;
    font-size: 14px;
    font-weight: 600;
    color: rgb(var(--picker-accent));
    border-radius: 7px;
    transition: background .2s ease;
  }

  &__remove {
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    padding: 0;
    color: #8a8ca5;
    border: 0;
    border-radius: 8px;
    background: transparent;
    cursor: pointer;
    transition: color .2s ease, background .2s ease;

    .feather-icon {
      display: flex;
    }

    &:hover {
      color: #dc3545;
      background: rgba(220, 53, 69, .08);
    }
  }
}

@keyframes d-file-picker-bounce {
  0%, 100% { transform: translateY(0); }
  40% { transform: translateY(-4px); }
  70% { transform: translateY(1px); }
}

@media (prefers-reduced-motion: reduce) {
  .d-file-picker * {
    animation: none !important;
    transition: none !important;
  }
}

.theme-dark .d-file-picker {
  &__box {
    background: #10163a;
    border-color: #414561;
  }

  &__title {
    color: #fff;
  }

  &__thumb {
    color: #c2c6dc;
    background: #262c49;
  }
}
</style>
