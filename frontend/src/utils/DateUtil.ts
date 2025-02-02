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

  /**
   * スラッシュの日付をハイフンの日付に変換する
   * ハイフンにするために スウェーデン のロケールを指定している
   * @param dateHyphen
   */
  static formatDateHyphen = (dateSlash: string) =>
    new Date(dateSlash).toLocaleDateString('sv-SE', {
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
    })
}
