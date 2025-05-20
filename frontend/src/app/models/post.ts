export class Post{
  constructor(
    public id: number,
    public title: string,
    public content: string,
    public image: string,
    public category_id: number,
    public user_id: number,
    public created_at: any,
  )
  {}

}
