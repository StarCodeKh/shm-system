<template>
  <div class="d-file-picker" :class="{ 'has-file': !!value, 'is-image': isImage && !imageFailed }">

    <!-- Empty: dashed drop-style button -->
    <button v-if="!value" type="button" class="d-file-picker__empty" @click="$emit('browse')">
      <span class="d-file-picker__icon">
        <feather-icon icon="UploadIcon" svgClasses="h-5 w-5" />
      </span>
      <span class="d-file-picker__text">
        <span class="d-file-picker__title">{{ $t('upload_choose') }}</span>
        <span class="d-file-picker__hint">{{ hintText }}</span>
      </span>
      <span class="d-file-picker__browse">{{ $t('upload_browse') }}</span>
    </button>

    <!-- Chosen: preview + actions -->
    <div v-else class="d-file-picker__chosen">
      <button type="button" class="d-file-picker__preview" :title="$t('upload_view')" @click="$emit('view', value)">
        <img v-if="isImage && !imageFailed" :src="value" alt="" @error="imageFailed = true">
        <span v-else class="d-file-picker__type" :style="{ '--type': typeInfo.color }">
          <feather-icon :icon="typeInfo.icon" svgClasses="h-5 w-5" />
          <small>{{ extension || 'FILE' }}</small>
        </span>
        <span class="d-file-picker__zoom">
          <feather-icon icon="EyeIcon" svgClasses="h-4 w-4" />
        </span>
      </button>

      <div class="d-file-picker__info">
        <span class="d-file-picker__name" :title="fileName">{{ fileName }}</span>
        <span class="d-file-picker__meta">
          <feather-icon icon="CheckCircleIcon" svgClasses="h-3 w-3" />
          {{ $t('upload_selected') }}
        </span>
      </div>

      <div class="d-file-picker__actions">
        <button type="button" class="d-file-picker__btn" :title="$t('upload_change')" @click="$emit('browse')">
          <feather-icon icon="RefreshCwIcon" svgClasses="h-4 w-4" />
          <span>{{ $t('upload_change') }}</span>
        </button>
        <button type="button" class="d-file-picker__btn d-file-picker__btn--danger" :title="$t('upload_remove')"
          @click="$emit('clear')">
          <feather-icon icon="Trash2Icon" svgClasses="h-4 w-4" />
        </button>
      </div>
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
}
</script>

<style lang="scss">
.d-file-picker {
  --picker-accent: var(--vs-primary);
  width: 100%;

  // ---------------------------------------------------------------- Empty state
  &__empty {
    position: relative;
    display: flex;
    align-items: center;
    gap: 14px;
    width: 100%;
    min-height: 64px;
    padding: 12px 18px 12px 14px;
    font-family: inherit;
    text-align: left;
    border: 1.5px dashed rgba(var(--brand-gold, 180, 140, 60), .75);
    border-radius: 14px;
    background: #fff;
    cursor: pointer;
    overflow: hidden;
    transition: border-color .2s ease, background .2s ease, box-shadow .2s ease, transform .2s ease;

    // Soft sweep on hover
    &::after {
      content: '';
      position: absolute;
      top: 0;
      bottom: 0;
      left: -40%;
      width: 35%;
      background: linear-gradient(100deg, transparent, rgba(var(--picker-accent), .08), transparent);
      transform: skewX(-20deg);
      opacity: 0;
      pointer-events: none;
    }

    &:hover {
      border-color: rgb(var(--picker-accent));
      background: rgba(var(--picker-accent), .03);
      box-shadow: 0 8px 20px -14px rgba(var(--picker-accent), .9);
      transform: translateY(-1px);

      &::after {
        animation: d-file-picker-shine .8s ease forwards;
      }

      .d-file-picker__icon {
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

    &:active {
      transform: translateY(0) scale(.995);
    }

    &:focus-visible {
      outline: none;
      border-color: rgb(var(--picker-accent));
      box-shadow: 0 0 0 3px rgba(var(--picker-accent), .2);
    }
  }

  &__icon {
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 10px;
    color: #4b4d63;
    background: #f3f5f2;
    transition: background .2s ease, color .2s ease;

    .feather-icon {
      display: flex;
    }
  }

  &__text {
    display: flex;
    align-items: baseline;
    flex-wrap: wrap;
    gap: 2px 10px;
    flex: 1;
    min-width: 0;
  }

  &__title {
    font-size: 1rem;
    font-weight: 600;
    color: #2c2e45;
  }

  &__hint {
    font-size: .8rem;
    color: #8a8ca5;
  }

  &__browse {
    flex-shrink: 0;
    padding: 6px 12px;
    font-size: .95rem;
    font-weight: 600;
    color: rgb(var(--picker-accent));
    border-radius: 8px;
    transition: background .2s ease;
  }

  // ---------------------------------------------------------------- Chosen state
  &__chosen {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 10px 12px 10px 10px;
    border: 1px solid #e6e8f0;
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 2px 10px -6px rgba(23, 23, 74, .15);
    animation: d-file-picker-in .35s cubic-bezier(.22, 1, .36, 1);
  }

  &__preview {
    position: relative;
    flex-shrink: 0;
    width: 64px;
    height: 64px;
    padding: 0;
    border: 1px solid #eceef4;
    border-radius: 10px;
    background: #f7f8fb;
    overflow: hidden;
    cursor: zoom-in;

    img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      transition: transform .3s ease;
    }

    &:hover {
      img {
        transform: scale(1.08);
      }

      .d-file-picker__zoom {
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
    gap: 2px;
    width: 100%;
    height: 100%;
    color: rgb(var(--type));
    background: rgba(var(--type), .1);

    small {
      font-size: .65rem;
      font-weight: 700;
      letter-spacing: .04em;
      text-transform: uppercase;
    }
  }

  &__info {
    display: flex;
    flex-direction: column;
    flex: 1;
    min-width: 0;
    line-height: 1.4;
  }

  &__name {
    font-size: .9rem;
    font-weight: 600;
    color: #2c2e45;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  &__meta {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: .78rem;
    color: rgb(var(--picker-accent));
  }

  &__actions {
    display: flex;
    gap: 6px;
    flex-shrink: 0;
  }

  &__btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    height: 34px;
    padding: 0 12px;
    font-family: inherit;
    font-size: .85rem;
    font-weight: 500;
    color: #4b4d63;
    border: 1px solid #e3e5ee;
    border-radius: 9px;
    background: #fff;
    cursor: pointer;
    transition: color .2s ease, border-color .2s ease, background .2s ease;

    .feather-icon {
      display: flex;
    }

    &:hover {
      color: rgb(var(--picker-accent));
      border-color: rgba(var(--picker-accent), .5);
      background: rgba(var(--picker-accent), .05);

      .feather-icon svg {
        animation: d-file-picker-spin .5s ease;
      }
    }

    &--danger {
      padding: 0 9px;

      &:hover {
        color: #dc3545;
        border-color: rgba(220, 53, 69, .4);
        background: rgba(220, 53, 69, .06);

        .feather-icon svg {
          animation: d-file-picker-shake .4s ease;
        }
      }
    }
  }

  @media (max-width: 480px) {
    &__btn span {
      display: none;
    }

    &__btn {
      padding: 0 9px;
    }
  }
}

@keyframes d-file-picker-in {
  from { opacity: 0; transform: translateY(6px) scale(.98); }
  to { opacity: 1; transform: none; }
}

@keyframes d-file-picker-shine {
  0% { left: -40%; opacity: 0; }
  20% { opacity: 1; }
  100% { left: 120%; opacity: 0; }
}

@keyframes d-file-picker-bounce {
  0%, 100% { transform: translateY(0); }
  40% { transform: translateY(-4px); }
  70% { transform: translateY(1px); }
}

@keyframes d-file-picker-spin {
  from { transform: rotate(0); }
  to { transform: rotate(180deg); }
}

@keyframes d-file-picker-shake {
  0%, 100% { transform: rotate(0); }
  25% { transform: rotate(-12deg); }
  75% { transform: rotate(12deg); }
}

@media (prefers-reduced-motion: reduce) {
  .d-file-picker *,
  .d-file-picker *::after {
    animation: none !important;
    transition: none !important;
  }
}

.theme-dark .d-file-picker {
  &__empty,
  &__chosen,
  &__btn {
    background: #10163a;
    border-color: #414561;
  }

  &__title,
  &__name {
    color: #fff;
  }

  &__icon,
  &__preview {
    background: #262c49;
  }

  &__icon,
  &__btn {
    color: #c2c6dc;
  }
}
</style>
