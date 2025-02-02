export class DateUtil {
  /**
   * ハイフンの日付をスラッシュの日付に変換する
   * @param dateHyphen
   */
  static formatJpDateSlash = (dateHyphen: string) =>
    new Date(dateHyphen).toLocaleDateString('ja-JP', {
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
    })
}
